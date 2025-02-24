<div class="legals_content">
    <form id="saveLegalsContent" method="post" class="form-horizontal" action="" enctype="multipart/form-data">
        <input type="hidden" name="record" value="<?php echo isset($_REQUEST['id'])?$_REQUEST['id']:''; ?>" />
        <input type="hidden" name="step" value="legals" />
        <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
        <div class="row">
            <div class="col-6">
                <div class="mb-3">
                    <label for="ruleEditor" class="form-label">Competition Rules*</label>
                    <textarea id="rule_editor" name="competition_rules" required><?php echo (isset($recordData['competition_rules']) && $recordData['competition_rules'] != '') ? $recordData['competition_rules'] : ''; ?></textarea>
                </div>
            </div>
            <div class="col-6">
                <div class="mb-3">
                    <label for="faqEditor" class="form-label">FAQS*</label>
                    <textarea id="faq_editor" name="faq"><?php echo (isset($recordData['faq']) && $recordData['faq'] != '') ? $recordData['faq'] : ''; ?></textarea required>
                </div>
            </div>
        </div>
    </form>
</div>