<div id="competitions-plugin-container">
    <div class="question_content">
        <form id="savQuestionContent" method="post" class="form-horizontal"
            action="<?php echo admin_url('admin-ajax.php'); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_global_question" />
            <input type="hidden" name="mode" value="create" />
            <div class="header_container">
                <div class="container-fluid">

                    <div class="row">
                        <h3 class="col-md-6 header-text">Add a Global Question</h3>
                        <div class="col-md-6 text-end">
                            <a href="<?php echo admin_url('admin.php?page=question-settings'); ?>"
                                class="btn btn-sm btn-default move_back" id="back_btn">Back</a>
                            <button type="submit" class="btn btn-sm btn-accent create_global_question"
                                id="publish_btn">Publish</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row pt-150">
                <div class="col-4 mx-auto">

                    <span class="form-check form-switch d-flex justify-content-center">
                        <label class="form-check-label mx-3 text-secondary custom_label" for="custom">Enable</label>
                        <input type="hidden" name="enabled" value="0">
                        <label class="switch">
                            <input type="checkbox" id="globalcustom" name="enabled" value="1" checked>
                            <span class="sliders rounds"></span>
                        </label>
                    </span>

                    <div class="mb-3 mt-3">
                        <label for="email" class="form-label custom_label">Question</label>
                        <input type="text" class="form-control customdisable" id="comp_question"
                            placeholder="Enter a question" name="question" value="">
                    </div>
                    <div class="mb-3 ans_content">
                        <label for="answer1" class="form-label custom_label d-flex justify-content-between">Answer 1
                            <span class="form-check form-switch"><span class="pe-2 slider_label">Correct</span>
                                <label class="switchs">
                                    <input type="checkbox" id="comp_ans_1" name="correct_option" value="answer1"
                                        class="correct-answer customdisable">
                                    <span class="sliders rounds"></span>
                                </label>
                            </span>
                        </label>
                        <input type="text" class="form-control customdisable question-ans" id="answer1"
                            placeholder="Enter an answer" name="answer1" value="">
                    </div>
                    <div class="mb-3 ans_content">
                        <label for="answer2" class="form-label custom_label d-flex justify-content-between">Answer 2
                            <span class="form-check form-switch"><span class="pe-2 slider_label">Correct</span>
                                <label class="switchs">
                                    <input class="correct-answer customdisable" type="checkbox" id="comp_ans_2"
                                        name="correct_option" value="answer2">
                                    <span class="sliders rounds"></span>
                                </label>
                            </span>
                        </label>
                        <input type="text" class="form-control customdisable question-ans" id="answer2"
                            placeholder="Enter an answer" name="answer2" value="">
                    </div>
                    <div class="mb-3 ans_content">
                        <label for="answer3" class="form-label custom_label d-flex justify-content-between">Answer 3
                            <span class="form-check form-switch">
                                <span class="pe-2 slider_label">Correct</span>
                                <label class="switchs">
                                    <input class="correct-answer customdisable" type="checkbox" id="comp_ans_3"
                                        name="correct_option" value="answer3">
                                    <span class="sliders rounds"></span>
                                </label>
                            </span>
                        </label>
                        <input type="text" class="form-control customdisable question-ans" id="answer3"
                            placeholder="Enter an answer" name="answer3" value="">
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>