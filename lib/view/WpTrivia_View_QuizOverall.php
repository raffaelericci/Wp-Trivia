<?php

/**
 * @property WpTrivia_Model_Quiz[]  quizItems
 * @property int quizCount
 * @property int perPage
 */
class WpTrivia_View_QuizOverall extends WpTrivia_View_View
{

    public function show()
    {
        ?>
        <style>
            .column-shortcode {
                width: 100px;
            }

            .column-shortcode_leaderboard {
                width: 160px;
            }

            @media screen and (max-width: 782px) {
                .wpTrivia_InfoBar {
                    display: none;
                }
            }

            #wpTrivia_tab_donat {
                float: right;
                height: 28px;
                margin: 0 0 0 6px;
                border: 1px solid #ddd;
                border-top: none;
                box-shadow: 0 1px 1px -1px rgba(0,0,0,.1);
                background: #FFDB94;
            }

            #wpTrivia_tab_donat > a {
                color: #3A3A3A !important;
                font-weight: bold !important;
            }

            #wpTrivia_tab_donat > a:after{
                content: '' !important;
                padding: 0 5px 0 5px !important;
            }

        </style>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {

                function initGlobal() {
                    var isEmpty = function (str) {
                        str = $.trim(str);
                        return (!str || 0 === str.length);
                    };

                    var ajaxPost = function (func, data, success) {
                        var d = {
                            action: 'wp_trivia_admin_ajax',
                            func: func,
                            data: data
                        };

                        $.post(ajaxurl, d, success, 'json');
                    };

                    $('.wpTrivia_import').click(function () {
                        showWpTriviaModalBox('', 'wpTrivia_importList_box');

                        return false;
                    });

                    return true;
                }

                initGlobal();

                function showWpTriviaModalBox(title, id) {
                    var width = Math.min($('.wpTrivia_quizOverall').width() - 50, 600);
                    var a = '#TB_inline?width=' + width + '&inlineId=' + id;

                    tb_show(title, a, false);
                }

                function getCheckedItems() {
                    var items = $('[name="quiz[]"]:checked').map(function (i) {
                        var $this = $(this);
                        var $tr = $this.parents('tr');

                        var item = {
                            ID: $this.val(),
                            name: $.trim($tr.find('.name .row-title').text())
                        };

                        return item;
                    }).get();

                    return items;
                }

                function handleExportAction() {
                    var items = getCheckedItems();

                    if (!items || !items.length)
                        return false;

                    var $exportBox = $('.wpTrivia_exportList');
                    var $hiddenBox = $exportBox.find('#exportHidden').empty();
                    var $ulBox = $exportBox.find('ul').empty();

                    $.each(items, function (i, v) {
                        $ulBox.append(
                            $('<li>').text(v.name)
                        );

                        $hiddenBox.append(
                            $('<input type="hidden" name="exportIds[]">').val(v.ID)
                        );
                    });

                    showWpTriviaModalBox('', 'wpTrivia_exportList_box');

                    return true;
                }

                function handleDeleteAction() {
                    var items = getCheckedItems();
                    var $form = $('#deleteForm').empty();

                    $.each(items, function (i, v) {
                        $form.append(
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'ids[]',
                                value: v.ID
                            })
                        );
                    });

                    $form.submit();
                }

                function handleAction(action) {
                    switch (action) {
                        case 'export':
                            handleExportAction();
                            return false;
                        case 'delete':
                            handleDeleteAction();
                            return false;
                    }

                    return true;
                }

                $('#doaction').click(function () {
                    return handleAction($('[name="action"]').val());
                });

                $('#doaction2').click(function () {
                    return handleAction($('[name="action2"]').val());
                });

                $('.wpTrivia_delete').click(function (e) {
                    var b = confirm(wpTriviaLocalize.delete_msg);

                    if (!b) {
                        e.preventDefault();
                        return false;
                    }

                    return true;
                });

                $('#screen-meta-links').append($('#wpTrivia_tab_donat').show());

            });
        </script>

        <?php
        add_thickbox();

        $this->showImportListBox();
        $this->showExportListBox();
        ?>

        <div id="wpTrivia_tab_donat" style="display: none;" class="hide-if-no-js screen-meta-toggle">
            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KCZPNURT6RYXY" class="button show-settings" target="_blank"><?php _e('Donate', 'wp-trivia'); ?></a>
        </div>

        <div class="wrap wpTrivia_quizOverall" style="">
            <h2>
                <?php _e('Quiz overview', 'wp-trivia'); ?>
                <?php if (current_user_can('wpTrivia_add_quiz')) { ?>
                    <a class="add-new-h2" href="admin.php?page=wpTrivia&action=addEdit"><?php echo __('Add quiz',
                            'wp-trivia'); ?></a>
                <?php }
                if (current_user_can('wpTrivia_import')) { ?>
                    <a class="add-new-h2 wpTrivia_import" href="#"><?php echo __('Import', 'wp-trivia'); ?></a>
                <?php } ?>
            </h2>

            <form action="?page=wpTrivia&action=deleteMulti" method="post" style="display: none;" id="deleteForm">

            </form>

            <div>
                <div class="wpTrivia_InfoBar" style="display: none; margin-top:-36px; float: right;">

                    <div style="background-color: #FFFBCC; padding: 6px; border: 1px solid #E6DB55; float: left;">
                        <strong><?php _e('You need special Wp-Trivia modification for your website?',
                                'wp-trivia'); ?></strong><br>
                        <a class="button-primary" href="admin.php?page=wpTrivia&module=info_adaptation"
                           style="margin-top: 5px;"><?php _e('Learn more', 'wp-trivia'); ?></a>
                    </div>

                    <div
                        style="background-color: #FFFBCC; padding: 3px 35px; border: 1px solid #E6DB55; float: left; margin-left: 10px;">
                        <span style="font-weight: bold; margin-left: 15px;"><?php _e('Wp-Trivia',
                                'wp-trivia'); ?></span>

                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="BF9JT56N7FAQG">
                            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif"
                                   border="0" name="submit"
                                   alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
                            <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1"
                                 height="1">
                        </form>
                    </div>

                    <div style="clear: both;"></div>
                </div>
                <div style="clear: both;"></div>
            </div>

            <p style="margin-bottom: 0; display: none;">
                <?php if (current_user_can('wpTrivia_add_quiz')) { ?>
                    <a class="button-secondary" href="admin.php?page=wpTrivia&action=addEdit"><?php echo __('Add quiz',
                            'wp-trivia'); ?></a>
                <?php }
                if (current_user_can('wpTrivia_import')) { ?>
                    <a class="button-secondary wpTrivia_import" href="#"><?php echo __('Import', 'wp-trivia'); ?></a>
                <?php } ?>
            </p>

            <form action="" method="get">
                <input type="hidden" name="page" value="wpTrivia">

                <?php
                $overviewTable = $this->getTable();
                $overviewTable->prepare_items();

                ?>
                <p class="search-box">
                    <?php $overviewTable->search_box(__('Search'), 'search_id'); ?>

                </p>

                <?php
                $overviewTable->display();
                ?>

            </form>

        </div>

        <?php
    }

    /**
     * @return WpTrivia_View_QuizOverallTable
     */
    protected function getTable()
    {
        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }

        return new WpTrivia_View_QuizOverallTable($this->quizItems, $this->quizCount, $this->perPage);
    }

    protected function showImportListBox()
    {
        ?>

        <div id="wpTrivia_importList_box" style="display: none;">
            <div class="wpTrivia_importList">
                <form action="admin.php?page=wpTrivia&module=importExport&action=import" method="POST"
                      enctype="multipart/form-data">
                    <h3 style="margin-top: 0;"><?php _e('Import', 'wp-trivia'); ?></h3>

                    <p><?php _e('Import only *.wpq or *.xml files from known and trusted sources.',
                            'wp-trivia'); ?></p>

                    <div style="margin-bottom: 10px">
                        <?php
                        $maxUpload = (int)(ini_get('upload_max_filesize'));
                        $maxPost = (int)(ini_get('post_max_size'));
                        $memoryLimit = (int)(ini_get('memory_limit'));
                        $uploadMB = min($maxUpload, $maxPost, $memoryLimit);
                        ?>
                        <input type="file" name="import" accept=".wpq,.xml"
                               required="required"> <?php printf(__('Maximal %d MiB', 'wp-trivia'), $uploadMB); ?>
                    </div>
                    <input class="button-primary" name="exportStart" id="exportStart"
                           value="<?php _e('Start import', 'wp-trivia'); ?>" type="submit">
                </form>
            </div>
        </div>

        <?php
    }

    protected function showExportListBox()
    {
        ?>

        <div id="wpTrivia_exportList_box" style="display: none;">
            <div class="wpTrivia_exportList">
                <form action="admin.php?page=wpTrivia&module=importExport&action=export&noheader=true" method="POST">
                    <h3 style="margin-top: 0;"><?php _e('Export', 'wp-trivia'); ?></h3>

                    <p><?php echo __('Choose the respective question, which you would like to export and press on "Start export"',
                            'wp-trivia'); ?></p>
                    <ul></ul>
                    <div style="clear: both; margin-bottom: 10px;"></div>
                    <div id="exportHidden"></div>
                    <div style="margin-bottom: 15px;">
                        <?php _e('Format:'); ?>
                        <label><input type="radio" name="exportType" value="wpq"
                                      checked="checked"> <?php _e('*.wpq'); ?></label>
                        <?php _e('or'); ?>
                        <label><input type="radio" name="exportType" value="xml"> <?php _e('*.xml'); ?></label>
                    </div>
                    <input class="button-primary" name="exportStart" id="exportStart"
                           value="<?php echo __('Start export', 'wp-trivia'); ?>" type="submit">
                </form>
            </div>
        </div>

        <?php
    }
}
