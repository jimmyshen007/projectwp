
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/11/17
 * Time: 2:06 AM
 */

get_header();

function createCheckBox($cid, $clabel, $cvalue) { ?>
        <div class="form-group col-md-4 cancel-left-padding">
            <div class="checkbox" style="padding-top: 0px">
                <label>
                    <input id="<?php echo $cid; ?>" name="<?php echo $cid; ?>"
                       type="checkbox" value="<?php echo $cvalue; ?>"> <?php echo __($clabel, 'materialwp'); ?>
                </label>
            </div>
        </div>
<?php
}

function createInput($iid, $ilabel, $type='text', $iph='', $required=true, $width="col-md-4")
{
    if (empty($iph)) {
        $iph = $ilabel;
    }
    ?>
    <div class="form-group <?php echo $width; ?>">
        <label class="control-label"><?php echo __($ilabel, 'materialwp'); ?></label>
        <input id="<?php echo $iid; ?>"
               name="<?php echo __($iid, 'materialwp'); ?>" maxlength="200" type="<?php echo $type; ?>"
               <?php if($required) { echo 'required="required"'; } ?> class="form-control"
               placeholder="<?php echo __($iph, 'materialwp'); ?>">
    </div>
    <?php
}

function createSelect($sid, $soptions, $slabel, $required=true, $width="col-md-4")
{?>
    <div class="form-group <?php echo $width; ?>">
        <label class="control-label"><?php echo __($slabel, 'materialwp'); ?></label>
        <select id="<?php echo $sid; ?>"
               name="<?php echo __($sid, 'materialwp'); ?>"
                <?php if($required) { echo 'required'; } ?>  class="form-control">
            <?php for($i = 0; $i < count($soptions); $i++){
                echo '<option value="' . $soptions[$i]['value'] . '">' . $soptions[$i]['label'] . '</option>';
            } ?>
        </select>
    </div>
    <?php
} ?>
<style>
    .heading-custom {
        margin-top: 20px;
        padding-top: 20px;
    }
    .premium-form-wrapper {
        margin: auto;
        position: relative;
        width: 90%;
        display: block;
    }

    .stepwizard-step p {
        margin-top: 10px;
    }
    .stepwizard-row {
        display: table-row;
    }
    .stepwizard {
        display: table;
        width: 90%;
        position: relative;
        left: 5%;
    }
    .stepwizard-step button[disabled] {
        opacity: 1 !important;
        filter: alpha(opacity=100) !important;
        background-color: grey;
    }
    .stepwizard-row:before {
        top: 27px;
        bottom: 0;
        position: absolute;
        content: " ";
        width: 100%;
        height: 1px;
        background-color: #ccc;
        z-order: 0;
    }
    .stepwizard-step {
        display: table-cell;
        text-align: center;
        position: relative;
    }
    .btn-circle {
        width: 40px;
        height: 40px;
        text-align: center;
        padding: 6px 0;
        font-size: 16px;
        line-height: 1.728571429;
        border-radius: 20px;
    }
    .cancel-left-padding{
        padding-left: 0px;
    }
    #word-cloud {
        margin: 0 auto;
        width: 80%;
        max-width: 600px;
        height: 500px;
        overflow: hidden;
    }
    .bootstrap-tagsinput {
        border: 0px !important;
        box-shadow: none !important;
    }
</style>

    <div class="container">
            <div id="primary" class="col-md-12 col-lg-12">
                <main id="main" class="site-main" role="main">
                    <div class="panel panel-default panel-custom">
                        <div class="panel-heading">
                            <img src="" />
                            <h2><?php echo __('Premium Service', 'materialwp') ?></h2>
                        </div>
                        <div class="panel-body">
                            <div class="stepwizard">
                                <div class="stepwizard-row setup-panel">
                                    <div class="stepwizard-step">
                                        <a href="#step-1" type="button" class="btn btn-primary btn-raised btn-circle">1</a>
                                        <p>Choose Service</p>
                                    </div>
                                    <div class="stepwizard-step">
                                        <a href="#step-2" type="button" class="btn disabled btn-default btn-raised btn-circle need-cloud" disabled="disabled">2</a>
                                        <p>Information</p>
                                    </div>
                                    <div class="stepwizard-step">
                                        <a href="#step-3" type="button" class="btn disabled btn-default btn-raised btn-circle" disabled="disabled">3</a>
                                        <p>Payment</p>
                                    </div>
                                </div>
                            </div>

                            <form role="form" action="" method="post">
                                <div class="setup-content" id="step-1">
                                    <div class="premium-form-wrapper">
                                        <h3 class="heading-custom"><?php echo __('Housing Service', 'materialwp') ?></h3>
                                            <?php createCheckBox('housing', 'Find your lovely Oversea Home ($500 AUD)', 'housing'); ?>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        <h3 class="heading-custom"><?php echo __('Arrival Services', 'materialwp') ?></h3>
                                            <?php createCheckBox('pickup', 'Aiport pickup ($100 AUD)', 'pickup'); ?>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        <h3 class="heading-custom"><?php echo __('Other Services', 'materialwp') ?></h3>
                                            <?php createCheckBox('electrogas', 'Establish electricity and gas Account ($20 AUD)', 'electrogas'); ?>
                                            <?php createCheckBox('cellphone', 'Establish cellphone account ($20 AUD)', 'cellphone'); ?>
                                            <?php createCheckBox('internet', 'Establish internet account ($20 AUD)', 'internet'); ?>
                                        <input type="hidden" name="total" value="0">
                                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="button"><?php echo __('Next', 'materialwp'); ?></button>
                                    </div>
                                </div>
                                <div class="setup-content" id="step-2">
                                    <div class="premium-form-wrapper">
                                        <h3><?php echo __('Housing Information', 'materialwp'); ?></h3>
                                        <div class="row">
                                            <?php createInput('budget', 'Budget', 'number', 'in AUD') ?>
                                        </div>
                                        <h3><?php echo __('Personal Information', 'materialwp'); ?></h3>
                                        <div class="row">
                                            <?php createInput('client_name', 'Name', 'text', ' ');
                                                  createInput('email', 'Email', 'text', ' ');
                                                  createInput('cellphone', 'Cellphone', 'text', ' '); ?>
                                        </div>
                                        <div class="row">
                                            <?php createInput('dob', 'Date of Birth');
                                                  createSelect('sex', array(
                                                        array('label' => 'Male',
                                                              'value' => 'male'),
                                                        array('label' => 'Female',
                                                              'value' => 'female'),
                                                        array('label' => 'Other',
                                                              'value' => 'other')
                                                  ), 'Male/Female');
                                                  createInput('arrival_date', 'Arrival Date'); ?>
                                        </div>
                                        <h3><?php echo __('Where to stay', 'materialwp'); ?></h3>
                                        <div class="row">
                                            <?php createInput('placename', 'Place Name', 'text', ' ', false, 'col-md-3');
                                            createInput('city', 'City', 'text', ' ', true, 'col-md-3');
                                            createInput('state', 'State', 'text', ' ', true, 'col-md-3');
                                            createSelect('country', array(
                                                array('label' => 'Australia',
                                                'value' => 'australia')
                                            ), 'Country', true, 'col-md-3'); ?>
                                        </div>
                                        <h3><?php echo __('Additional Information', 'materialwp'); ?></h3>
                                        <div class="row">
                                            <?php
                                            createSelect('living', array(
                                                array('label' => 'Entire house',
                                                    'value' => 'entire_house'),
                                                array('label' => 'private_room',
                                                    'value' => 'australia')
                                            ), 'Prefer Living', true, 'col-md-3');
                                            createSelect('have_pet', array(
                                                array('label' => 'No',
                                                    'value' => 'no'),
                                                array('label' => 'Yes',
                                                    'value' => 'yes')), 'Pets', false, 'col-md-3');
                                            createSelect('parking', array(
                                                array('label' => 'No',
                                                    'value' => 'No'),
                                                array('label' => 'Yes',
                                                    'value' => 'yes')), 'Parking', false, 'col-md-3');
                                            createSelect('smoke', array(
                                                array('label' => 'No',
                                                    'value' => 'No'),
                                                array('label' => 'Yes',
                                                    'value' => 'yes')), 'Smoke or not', false, 'col-md-3'); ?>
                                        </div>
                                        <h3><?php echo __('Describe yourself', 'materialwp'); ?></h3>
                                        <input id="word-cloud-input" type="text" data-role="tagsinput">
                                        <div id="word-cloud"></div>
                                        <button class="btn btn-primary prevBtn btn-lg pull-left" type="button"><?php echo __('Previous', 'materialwp'); ?></button>
                                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="button"><?php echo __('Next', 'materialwp'); ?></button>
                                    </div>
                                </div>
                                <div class="setup-content" id="step-3">
                                    <div class="premium-form-wrapper">
                                            <button class="btn btn-primary prevBtn btn-lg pull-left" type="button"><?php echo __('Previous', 'materialwp'); ?></button>
                                            <button class="btn btn-success btn-lg pull-right" type="submit"><?php echo __('Submit', 'materialwp'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </main><!-- #main -->
            </div><!-- #primary -->
    </div> <!-- .container -->
    <script>
        $(document).ready(function () {
            $( "input#dob" ).datepicker({changeMonth: true,
                changeYear: true,
                defaultDate: new Date(1999, 1, 1)});
            $( "input#dob" ).datepicker( "option", "dateFormat", "yy-mm-dd");
            $( "input#arrival_date" ).datepicker({ minDate:0});
            $( "input#arrival_date" ).datepicker( "option", "dateFormat", "yy-mm-dd");

            var cloud = d3.layout.cloud;

            var fill = d3.scale.category20();

            var layout = cloud()
                .size([$(window).width() / 2, 400])
                .words([
                    "Like cooking", "Dancer", "Artist", "Music lover", "Nerd", "Professional", "Geek",
                    "Pretty girl", "Xiaoxianrou"].map(function(d) {
                    return {text: d, size: 14 + Math.random() * 26};
                }))
                .padding(5)
                .rotate(function() { return ~~(Math.random() * 1) * 90; })
                .font("Impact")
                .fontSize(function(d) { return d.size; })
                .on("end", draw);

            var colorList = ['#0df', '#0cf', '#0cf', '#0cf', '#0cf', '#39d', '#90c5f0', '#90a0dd', '#90c5f0', '#a0ddff',
                '#99ccee', '#aab5f0'];
            $('#word-cloud-input').tagsinput({
                maxTags: 5,
                maxChars: 100
            });
            function draw(words) {
                d3.select("#word-cloud").append("svg")
                    .attr("width", layout.size()[0])
                    .attr("height", layout.size()[1])
                    .append("g")
                    .attr("transform", "translate(" + layout.size()[0] / 2 + "," + layout.size()[1] / 2 + ")")
                    .selectAll("a")
                    .data(words)
                    .enter().append("a").on('click', function(d, i){
                        $('#word-cloud-input').tagsinput('add', d.text);
                    }).append("text")
                    .style("font-size", function(d) { return d.size + "px"; })
                    .style("font-family", "Impact")
                    .style("fill", function(d, i) { return colorList[Math.round(Math.random() * (colorList.length-1))]; })
                    .attr("text-anchor", "middle")
                    .attr("transform", function(d) {
                        return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
                    })
                    .text(function(d) { return d.text; });
            }

            var navListItems = $('div.setup-panel div a'),
                allWells = $('.setup-content'),
                allNextBtn = $('.nextBtn'),
                allPrevBtn = $('.prevBtn');

            allWells.hide();

            navListItems.click(function (e) {
                e.preventDefault();
                var $target = $($(this).attr('href')),
                    $item = $(this);

                //if (!$item.hasClass('disabled')) {
                $item.removeClass('disabled');
                navListItems.removeClass('btn-primary').addClass('btn-default');
                $item.addClass('btn-primary');
                allWells.hide();
                $target.show();
                $target.find('input:eq(0)').focus();
                if($item.hasClass('need-cloud')){
                    layout.start();
                }
                //}
            });

            allPrevBtn.click(function(){
                var curStep = $(this).closest(".setup-content"),
                    curStepBtn = curStep.attr("id"),
                    prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

                prevStepWizard.removeAttr('disabled').trigger('click');
            });

            allNextBtn.click(function(){
                var curStep = $(this).closest(".setup-content"),
                    curStepBtn = curStep.attr("id"),
                    nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                    curInputs = curStep.find("input[type='text'],input[type='url']"),
                    isValid = true;

                $(".form-group").removeClass("has-error");
                for(var i=0; i<curInputs.length; i++){
                    if (!curInputs[i].validity.valid){
                        isValid = false;
                        $(curInputs[i]).closest(".form-group").addClass("has-error");
                    }
                }

                if (isValid)
                    nextStepWizard.removeAttr('disabled').trigger('click');
            });

            $('div.setup-panel div a.btn-primary').trigger('click');
        });
    </script>

<?php get_footer(); ?>