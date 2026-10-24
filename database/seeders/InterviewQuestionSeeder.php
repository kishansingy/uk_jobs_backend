<?php

namespace Database\Seeders;

use App\Models\InterviewCategory;
use App\Models\InterviewQuestion;
use Illuminate\Database\Seeder;

class InterviewQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            'General NHS' => [
                ['q' => 'What do the NHS core values mean to you personally?', 'd' => 'easy', 'g' => 'Refer to the NHS Constitution\'s 6 values: working together, respect and dignity, commitment to quality, compassion, improving lives, everyone counts.'],
                ['q' => 'How would you describe the structure of the NHS to a patient?', 'd' => 'medium', 'g' => 'Cover NHS England, ICBs, NHS Trusts, PCNs and community care. Keep it simple and patient-focused.'],
                ['q' => 'What do you understand by integrated care, and why does it matter?', 'd' => 'medium', 'g' => 'Discuss how integrated care systems bring together NHS, social care and voluntary sector to improve outcomes.'],
                ['q' => 'How do you stay up-to-date with changes happening in the NHS?', 'd' => 'easy', 'g' => 'Mention NHS England website, journals, CPD events, professional bodies and team discussions.'],
                ['q' => 'Why do you want to work in the NHS rather than the private sector?', 'd' => 'easy', 'g' => 'Focus on values alignment, universal care, job security, training opportunities and making a difference.'],
            ],
            'CV & Experience' => [
                ['q' => 'Walk me through your CV and explain your career choices so far.', 'd' => 'easy', 'g' => 'Use a clear chronological narrative. Highlight progression, decisions and what you learned at each stage.'],
                ['q' => 'Tell me about a time you went above and beyond for a patient or colleague.', 'd' => 'medium', 'g' => 'Use the STAR method: Situation, Task, Action, Result. Focus on measurable impact.'],
                ['q' => 'Describe a significant achievement in your current or most recent role.', 'd' => 'medium', 'g' => 'Quantify the achievement where possible. Link it to team or organisational goals.'],
                ['q' => 'What has been your biggest professional challenge and how did you overcome it?', 'd' => 'hard', 'g' => 'Show self-awareness, problem-solving, resilience and growth. Avoid blaming others.'],
                ['q' => 'Where do you see yourself professionally in five years?', 'd' => 'easy', 'g' => 'Show ambition balanced with realism. Link future goals to the role you are applying for.'],
            ],
            'Clinical Scenarios' => [
                ['q' => 'A patient deteriorates unexpectedly on your ward. What do you do?', 'd' => 'medium', 'g' => 'Follow the A-E assessment approach, call for help early, document clearly, communicate with the team.'],
                ['q' => 'How would you manage a patient who refuses treatment that you believe is in their best interest?', 'd' => 'hard', 'g' => 'Discuss capacity assessment, Mental Capacity Act, best interest decisions, involving family and ethics committee if needed.'],
                ['q' => 'Describe how you would prioritise your workload during a busy shift when short-staffed.', 'd' => 'medium', 'g' => 'Triage by clinical need, communicate early with seniors, delegate appropriately, document decisions.'],
                ['q' => 'A patient\'s family is unhappy with the care being provided. How do you handle this?', 'd' => 'medium', 'g' => 'Listen actively, acknowledge concerns, explain the care plan clearly, escalate if needed, document everything.'],
                ['q' => 'How do you ensure you maintain clinical accuracy when fatigued near the end of a long shift?', 'd' => 'hard', 'g' => 'Discuss structured handovers, checklists, buddy systems, admitting limitations and escalating when uncertain.'],
            ],
            'Communication' => [
                ['q' => 'Tell me about a time you had to explain a complex clinical situation to a patient or carer with low health literacy.', 'd' => 'medium', 'g' => 'Use plain language, teach-back method, written information, involve family if consented.'],
                ['q' => 'How do you communicate bad news sensitively and professionally?', 'd' => 'hard', 'g' => 'Reference SPIKES protocol: Setting, Perception, Invitation, Knowledge, Emotions, Strategy.'],
                ['q' => 'Describe a situation where poor communication led to a problem. What would you do differently?', 'd' => 'medium', 'g' => 'Show honesty, reflection and genuine learning. Focus on systemic improvements not just individual fixes.'],
                ['q' => 'How do you ensure clear handover of patient information between shifts?', 'd' => 'easy', 'g' => 'Discuss SBAR (Situation, Background, Assessment, Recommendation), structured tools, read-backs.'],
            ],
            'Teamwork' => [
                ['q' => 'Give an example of when you successfully worked as part of a multidisciplinary team.', 'd' => 'medium', 'g' => 'Emphasise your specific contribution, respect for others\' expertise, and shared patient outcome.'],
                ['q' => 'How do you handle a situation where you disagree with a decision made by a senior colleague?', 'd' => 'hard', 'g' => 'Raise concerns professionally through appropriate channels. Reference duty of candour and whistleblowing policy.'],
                ['q' => 'Describe how you support newly qualified colleagues or junior staff.', 'd' => 'easy', 'g' => 'Discuss mentoring, supervision, creating a safe learning environment, signposting resources.'],
                ['q' => 'Tell me about a time a team you were in was not working well. What did you do?', 'd' => 'hard', 'g' => 'Focus on constructive action: communication, clarifying roles, raising concerns early, seeking support from management.'],
            ],
            'Leadership' => [
                ['q' => 'Tell me about a time you led a team through a difficult change.', 'd' => 'hard', 'g' => 'Use STAR method. Demonstrate change management skills: communication, involving others, monitoring progress.'],
                ['q' => 'What is your approach to managing underperformance in your team?', 'd' => 'hard', 'g' => 'Early, private, supportive conversation. Set clear expectations, offer support, follow HR policy if needed.'],
                ['q' => 'How do you motivate your team during periods of high pressure?', 'd' => 'medium', 'g' => 'Regular check-ins, acknowledging effort, sharing wins, fair workload distribution, access to wellbeing support.'],
                ['q' => 'Describe your leadership style and give an example of when it was effective.', 'd' => 'medium', 'g' => 'Be specific about your style (transformational, servant, situational). Back it with a real example.'],
            ],
            'Safeguarding' => [
                ['q' => 'What would you do if you suspected a child you were treating had been abused?', 'd' => 'hard', 'g' => 'Discuss child safeguarding procedures, duty to report, not promising confidentiality, contacting the safeguarding lead.'],
                ['q' => 'Explain the difference between adult safeguarding and child safeguarding.', 'd' => 'medium', 'g' => 'Reference Care Act 2014 for adults and Children Act 1989/2004 for children. Discuss capacity and consent differences.'],
                ['q' => 'How would you respond if a patient disclosed domestic abuse?', 'd' => 'hard', 'g' => 'SAFE approach: Start by asking, Ask directly, Follow up, Enquire about safety. Document, refer to specialist if consent given.'],
            ],
            'Conflict Resolution' => [
                ['q' => 'Tell me about a time you dealt with an aggressive or upset patient or visitor.', 'd' => 'medium', 'g' => 'Describe de-escalation techniques, maintaining calm, calling for support, documenting the incident.'],
                ['q' => 'How do you manage conflict between two colleagues that is affecting the team?', 'd' => 'hard', 'g' => 'Address privately, listen to both sides, focus on behaviours not personalities, escalate to HR if unresolved.'],
                ['q' => 'Describe a time you had to deliver difficult feedback to a peer.', 'd' => 'medium', 'g' => 'Use constructive, behaviour-specific language. Describe the impact and offer support. Follow up.'],
            ],
            'Patient-Centred Care' => [
                ['q' => 'How do you involve patients in decisions about their own care?', 'd' => 'easy', 'g' => 'Discuss informed consent, shared decision-making, providing options, respecting patient preferences and values.'],
                ['q' => 'Describe a time you advocated for a patient\'s needs within the healthcare system.', 'd' => 'medium', 'g' => 'Show you can navigate the system on behalf of patients, liaise across teams and escalate appropriately.'],
                ['q' => 'How do you care for patients from different cultural or religious backgrounds?', 'd' => 'medium', 'g' => 'Cultural competence, asking open questions, involving interpreters, respecting dietary or religious needs without assumptions.'],
            ],
            'Equality & Diversity' => [
                ['q' => 'How do you ensure your practice is inclusive and free from unconscious bias?', 'd' => 'medium', 'g' => 'Discuss self-awareness, training, reflective practice, challenging discriminatory behaviour respectfully.'],
                ['q' => 'Tell me about a time you challenged discriminatory behaviour by a colleague.', 'd' => 'hard', 'g' => 'Address the behaviour, not the person. Reference the Equality Act 2010 and your organisation\'s policy.'],
                ['q' => 'What does being an ally in the workplace mean to you?', 'd' => 'easy', 'g' => 'Active listening, speaking up, mentoring underrepresented colleagues, educating yourself continually.'],
            ],
            'Continuing Development' => [
                ['q' => 'How do you keep your clinical knowledge and skills up-to-date?', 'd' => 'easy', 'g' => 'CPD portfolio, conferences, e-learning, journal clubs, peer supervision, professional body events.'],
                ['q' => 'Tell me about a piece of feedback you received that changed your practice.', 'd' => 'medium', 'g' => 'Show you welcome feedback, reflect genuinely and make meaningful changes. Describe the outcome.'],
                ['q' => 'What are your learning goals for the next 12 months and how will you achieve them?', 'd' => 'easy', 'g' => 'Be specific and link goals to the role requirements. Show a realistic action plan.'],
            ],
        ];

        foreach ($questions as $categoryName => $items) {
            $category = InterviewCategory::where('name', $categoryName)->first();
            if (! $category) continue;

            foreach ($items as $item) {
                InterviewQuestion::firstOrCreate(
                    [
                        'category_id' => $category->id,
                        'question'    => $item['q'],
                    ],
                    [
                        'guidance'   => $item['g'],
                        'difficulty' => $item['d'],
                        'active'     => true,
                    ]
                );
            }
        }
    }
}
