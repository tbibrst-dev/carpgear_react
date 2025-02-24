<div class="question_content">
    <form id="savQuestionContent" method="post" class="form-horizontal" action="" enctype="multipart/form-data">
        <input type="hidden" name="record" value="<?php echo isset($_REQUEST['id'])?$_REQUEST['id']:''; ?>" />
        <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
        <input type="hidden" name="step" value="question" />
        <input type="hidden" id="total_ticket_purchased" value="<?php echo $comp_tickets_purchased;?>" />
        <div class="row">
            <div class="col-xl-6 mx-auto">
                <span class="form-check form-switch d-flex justify-content-center">
                    <label class="form-check-label mx-3" for="custom">Global</label>
                    <input type="hidden" name="comp_question" value="0" />
                    <label class="switch">
                        <input type="checkbox"  id="globalcustom" name="comp_question" value="1" <?php echo (isset($recordData['comp_question']) && $recordData['comp_question'] == '1') ? 'checked' : ''; ?>>
                        <span class="sliders rounds"></span>
                    </label>
                    <label class="form-check-label mx-3 text-secondary custom_label" for="custom">Custom</label>
                </span>
                <div class="mb-3 mt-3">
                    <label for="email" class="form-label custom_label text-secondary">Question</label>
                    <input type="text" class="form-control customdisable" id="comp_question"
                        placeholder="Enter a question" name="question" disabled value="<?php echo (isset($recordData['question']) && $recordData['question'] != '') ? $recordData['question'] : ''; ?>">
                </div>
                <div class="mb-3 ans_content">
                    <label for="answer1"
                        class="form-label custom_label text-secondary d-flex justify-content-between">Answer 1
                        <span class="form-check form-switch"><span class="pe-2">Correct</span>
                            <label class="switchs">
                                <input type="checkbox" id="comp_ans_1" name="answer_1" value="" disabled class="correct-answer customdisable" <?php echo (isset($recordData['correct_answer']) && $recordData['correct_answer'] == $recordData['question_options']['answer1']) ? 'checked' : ''; ?>>
                                <span class="sliders rounds"></span>
                            </label>
                        </span>
                    </label>
                    <input type="text" class="form-control customdisable question-ans" id="answer1"
                        placeholder="Enter an answer" name="answer1" value="<?php echo (isset($recordData['question_options']['answer1']) && $recordData['question_options']['answer1'] != '') ? $recordData['question_options']['answer1'] : ''; ?>" disabled>
                </div>
                <div class="mb-3 ans_content">
                    <label for="answer2"
                        class="form-label custom_label text-secondary d-flex justify-content-between">Answer 2
                        <span class="form-check form-switch"><span class="pe-2">Correct</span>
                            <label class="switchs">
                                <input class="correct-answer customdisable" type="checkbox" id="comp_ans_2" name="answer_2" value="" disabled <?php echo (isset($recordData['correct_answer']) && $recordData['correct_answer'] == $recordData['question_options']['answer2']) ? 'checked' : ''; ?>>
                                <span class="sliders rounds"></span>
                            </label>
                        </span>
                    </label>
                    <input type="text" class="form-control customdisable question-ans" id="answer2"
                        placeholder="Enter an answer" name="answer2" value="<?php echo (isset($recordData['question_options']['answer2']) && $recordData['question_options']['answer2'] != '') ? $recordData['question_options']['answer2'] : ''; ?>" disabled>
                </div>
                <div class="mb-3 ans_content">
                    <label for="answer3"
                        class="form-label custom_label text-secondary d-flex justify-content-between">Answer 3
                        <span class="form-check form-switch">
                            <span class="pe-2">Correct</span>
                            <label class="switchs">
                                <input class="correct-answer customdisable" type="checkbox" id="comp_ans_3" name="answer_3" value="" disabled <?php echo (isset($recordData['correct_answer']) && $recordData['correct_answer'] == $recordData['question_options']['answer3']) ? 'checked' : ''; ?>>
                                <span class="sliders rounds"></span>
                            </label>
                        </span>
                    </label>
                    <input type="text" class="form-control customdisable question-ans" id="answer3"
                        placeholder="Enter an answer" name="answer3" value="<?php echo (isset($recordData['question_options']['answer3']) && $recordData['question_options']['answer3'] != '') ? $recordData['question_options']['answer3'] : ''; ?>" disabled>
                </div>

            </div>
        </div>
    </form>
</div>