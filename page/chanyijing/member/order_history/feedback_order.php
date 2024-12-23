<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$memberID = $user->memberID;

//----------------------------------------------------------------------------- 

if (is_post()) {
    // Input
    $productSatisfaction  = req('product_satisfaction');
    $serviceSatisfaction  = req('service_satisfaction');
    $teamSatisfaction     = req('team_satisfaction');
    $improvementSuggestions = req('improvement_suggestions');

    $_err = [];

    // Validate suggestion field
    if ($improvementSuggestions == '') {
        $_err['improvementSuggestions'] = 'Improvement suggestions are required.';
    } elseif (strlen($improvementSuggestions) > 500) {
        $_err['improvementSuggestions'] = 'Maximum length 500 characters';
    }

    // Output
    if (!$_err) {
        $stm = $_db->prepare('INSERT INTO feedback 
                              (product_satisfaction, service_satisfaction, team_satisfaction, improvement_suggestions, submit_time, memberID) 
                              VALUES (?, ?, ?, ?, NOW(), ?)');
        $stm->execute([$productSatisfaction, $serviceSatisfaction, $teamSatisfaction, $improvementSuggestions, $memberID]);

        temp('info', 'Thank you for your feedback!');
        redirect('feedback_thank.php');
    }
}

//----------------------------------------------------------------------------- 

$_title = 'Feedback Form';
include '../../../../_head.php';
?>

<div class="top-heading-space">
    <h3>Feedback Form</h3>
</div>

<div class="feedback-form-container">
    <img src="../../../../images/tar_grocer_icon.png" alt="tar grocer logo"/>
    <h2>We value your feedback.</h2>
    <p>Please complete the following form and help us improve our customer experience.</p><br/><hr/>

    <form method="POST" action="">
        <div class="form-group">
            <label for="product_satisfaction">How satisfied are you with our product?</label>
            <input type="range" id="product_satisfaction" name="product_satisfaction" min="1" max="5" step="1" value="3">
            <div class="slider-labels">
                <span>Very Unsatisfied</span>
                <span>Satisfied</span>
                <span>Very Satisfied</span>
            </div>
            <?= err('productSatisfaction') ?>
        </div>

        <div class="form-group">
            <label for="service_satisfaction">How satisfied are you with our service?</label>
            <input type="range" id="service_satisfaction" name="service_satisfaction" min="1" max="5" step="1" value="3">
            <div class="slider-labels">
                <span>Very Unsatisfied</span>
                <span>Satisfied</span>
                <span>Very Satisfied</span>
            </div>
            <?= err('serviceSatisfaction') ?>
        </div>

        <div class="form-group">
            <label for="team_satisfaction">How satisfied are you with our team?</label>
            <input type="range" id="team_satisfaction" name="team_satisfaction" min="1" max="5" step="1" value="3">
            <div class="slider-labels">
                <span>Very Unsatisfied</span>
                <span>Satisfied</span>
                <span>Very Satisfied</span>
            </div>
            <?= err('teamSatisfaction') ?>
        </div>

        <div class="form-group">
            <label for="improvement_suggestions">Tell us how we can improve:</label>
            <textarea id="improvement_suggestions" name="improvement_suggestions" rows="4" placeholder="Your suggestions..."></textarea>
            <?= err('improvementSuggestions') ?>
        </div>

        <button type="submit" class="pink-btn">Submit</button>
    </form>
</div>

<br/>
<button data-get="history_list.php?memberID=<?= $memberID ?>">Back</button>

<?php
include '../../../../_foot.php';
?>
