pipeline {
    agent any

    parameters {
        string(name: 'BRANCH',  defaultValue: 'develop', description: 'Branch to build')
        string(name: 'APP_ENV', defaultValue: 'staging',  description: 'Target environment')
    }

    environment {
        PHP_BIN      = '/usr/local/bin/php'
        COMPOSER_BIN = '/usr/local/bin/composer'
        DEPLOY_PATH  = credentials('DEPLOY_PATH')
        DEPLOY_HOST  = credentials('DEPLOY_HOST')
        DEPLOY_USER  = credentials('DEPLOY_USER')
        APP_ENV_FILE = credentials("env-${params.APP_ENV}")
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('PHP Version Check') {
            steps {
                sh "${PHP_BIN} --version"
                sh "${PHP_BIN} -r \"if(version_compare(PHP_VERSION,'8.2.0','<')){exit(1);}\""
            }
        }

        stage('Install Dependencies') {
            steps {
                dir('backend') {
                    sh "${COMPOSER_BIN} install --no-dev --no-interaction --prefer-dist --optimize-autoloader"
                }
            }
        }

        stage('Code Style (Pint)') {
            steps {
                dir('backend') {
                    sh "${PHP_BIN} vendor/bin/pint --test"
                }
            }
        }

        stage('Static Analysis (PHPStan)') {
            steps {
                dir('backend') {
                    sh "${PHP_BIN} vendor/bin/phpstan analyse --no-progress --memory-limit=512M"
                }
            }
        }

        stage('Tests') {
            environment {
                DB_CONNECTION = 'sqlite'
                DB_DATABASE   = ':memory:'
                QUEUE_CONNECTION = 'sync'
                CACHE_STORE   = 'array'
            }
            steps {
                dir('backend') {
                    sh "${PHP_BIN} artisan test --parallel --no-interaction"
                }
            }
        }

        stage('Migration Dry-run') {
            when { branch 'main' }
            steps {
                dir('backend') {
                    sh "${PHP_BIN} artisan migrate --pretend --no-interaction"
                }
            }
        }

        stage('Deploy') {
            when {
                anyOf { branch 'main'; branch 'develop' }
            }
            steps {
                withCredentials([sshUserPrivateKey(credentialsId: 'deploy-ssh-key', keyFileVariable: 'SSH_KEY')]) {
                    sh '''
                        RELEASE_DIR="${DEPLOY_PATH}/releases/$(date +%Y%m%d%H%M%S)"
                        ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} "
                            set -e
                            mkdir -p ${RELEASE_DIR}
                            cp -r ${DEPLOY_PATH}/current/. ${RELEASE_DIR}/ 2>/dev/null || true
                        "
                        rsync -az --delete \
                            --exclude='.env' \
                            --exclude='storage/' \
                            --exclude='vendor/' \
                            -e "ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no" \
                            backend/ ${DEPLOY_USER}@${DEPLOY_HOST}:${RELEASE_DIR}/

                        ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} "
                            set -e
                            cd ${RELEASE_DIR}
                            ln -sfn ${DEPLOY_PATH}/shared/.env .env
                            ln -sfn ${DEPLOY_PATH}/shared/storage storage
                            ${COMPOSER_BIN} install --no-dev --no-interaction --prefer-dist --optimize-autoloader
                            ${PHP_BIN} artisan config:cache
                            ${PHP_BIN} artisan route:cache
                            ${PHP_BIN} artisan view:cache
                            ${PHP_BIN} artisan migrate --force --no-interaction
                            ln -sfn ${RELEASE_DIR} ${DEPLOY_PATH}/current
                            ${PHP_BIN} artisan queue:restart
                            sudo systemctl reload php-fpm 2>/dev/null || true
                            # Keep last 5 releases
                            ls -dt ${DEPLOY_PATH}/releases/*/ | tail -n +6 | xargs rm -rf
                        "
                    '''
                }
            }
        }

        stage('Health Check') {
            when {
                anyOf { branch 'main'; branch 'develop' }
            }
            steps {
                sh '''
                    sleep 10
                    HEALTH_URL="https://${APP_ENV == 'production' ? 'api.example.com' : 'staging.example.com'}/api/health"
                    STATUS=$(curl -sf -o /dev/null -w "%{http_code}" ${HEALTH_URL} || echo "000")
                    if [ "${STATUS}" != "200" ]; then
                        echo "Health check failed with status ${STATUS}"
                        exit 1
                    fi
                    echo "Health check passed"
                '''
            }
        }
    }

    post {
        success {
            echo "Backend deployed successfully to ${params.APP_ENV}"
        }
        failure {
            echo "Backend deployment failed"
        }
    }
}
