<?php

/**
 * @property WpTrivia_Model_Quiz quiz
 * @property  WpTrivia_Model_Question[] questionItems
 * @property  int questionCount
 * @property int perPage
 */
class WpTrivia_View_QuestionOverall extends WpTrivia_View_View {

	public function show() {
?>
<style>
.sortTable td {
	cursor: move;
}
</style>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		var isEmpty = function(str) {
			str = $.trim(str);
			return (!str || 0 === str.length);
		};

		var ajaxPost = function(func, data, success) {
			var d = {
				action: 'wp_trivia_admin_ajax',
				func: func,
				data: data
			};

			$.post(ajaxurl, d, success, 'json');
		};

		function showWpTriviaModalBox(title, id, height) {
			var width = Math.min($('.wpTrivia_questionOverall').width() - 50, 600);
			var a = '#TB_inline?width='+ width +'&inlineId=' + id;

			if(height === true) {
				a += '&height=' + ($(window).height() - 100);
			}

			tb_show(title, a, false);
		}

		function getCheckedItems() {
			var items = $('[name="questions[]"]:checked').map(function (i) {
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

		$('#sortQuestionBtn').click(function () {
			var tbody = $('#wpTrivia_sortQuestion table tbody').empty().sortable();
			var data = {
				quizId: $('[name="quiz_id"]').val()
			};

			ajaxPost('loadQuestionsSort', data, function (json) {
				$.each(json, function (i, v) {
					tbody.append(
						$('<tr>').append(
							$('<td>')
								.text(v.title)
								.data('questionId', v.id)
						)
					);
				});
			});

			showWpTriviaModalBox('', 'wpTrivia_sortQuestion_box', true);
		});

		$('.saveQuestionSort').click(function () {
			var questionData = $('#wpTrivia_sortQuestion table tbody tr > td').map(function (i, v) {
				return  $(v).data('questionId');
			}).get();

			var data = {
				sort: questionData
			};

			ajaxPost('questionSaveSort', data, function (json) {
				location.href='?page=wpTrivia&module=question&quiz_id=' + $('[name="quiz_id"]').val();
			});
		});

		$('#wpTrivia_questionCopyBtn').click(function () {

			var list = $('#questionCopySelect').hide().empty();

			var data = {
				quizId: $('[name="quiz_id"]').val()
			};

			$('#loadDataImg').show();

			ajaxPost('questionaLoadCopyQuestion', data, function (json) {
				$.each(json, function(i, v) {
						var group = $(document.createElement('optgroup'))
							.attr('label', v.name);

						$.each(v.question, function(qi, qv) {
							$(document.createElement('option'))
								.val(qv.id)
								.text(qv.name)
								.appendTo(group);
						});

						list.append(group);

					});

					$('#loadDataImg').hide();
					list.show();
			});

			showWpTriviaModalBox('', 'wpTrivia_questionCopy_box', true);
		});

		$('.wpTrivia_delete').click(function(e) {
			var b = confirm(wpTriviaLocalize.delete_msg);

			if(!b) {
				e.preventDefault();
				return false;
			}

			return true;
		});

	});

</script>

		<?php
			add_thickbox();
			$this->showSortQuestionBox();
			$this->showCopyQuestionBox();
		?>


<div class="wrap wpTrivia_questionOverall">
	<h2>
		<?php printf(__('Quiz: %s', 'wp-trivia'), $this->quiz->getName()); ?>

		<?php if(current_user_can('wpTrivia_edit_quiz')) { ?>
			<a class="add-new-h2" href="?page=wpTrivia&module=question&action=addEdit&quiz_id=<?php echo $this->quiz->getId(); ?>"><?php _e('Add question', 'wp-trivia'); ?></a>
		<?php } ?>
	</h2>

	<p>
		<a class="" href="admin.php?page=wpTrivia"><?php _e('back to overview', 'wp-trivia'); ?></a>
	</p>

	<p>
		<?php if(current_user_can('wpTrivia_edit_quiz')) { ?>
			<a class="button-secondary" href="admin.php?page=wpTrivia&action=addEdit&quizId=<?php echo $this->quiz->getId(); ?>"><?php _e('Edit quiz', 'wp-trivia'); ?></a>
			<a class="button-secondary" id="sortQuestionBtn" href="#"><?php _e('Sort Question', 'wp-trivia'); ?></a>
			<a class="button-secondary" href="#" id="wpTrivia_questionCopyBtn"><?php _e('Copy questions from another Quiz', 'wp-trivia'); ?></a>
		<?php } ?>

	</p>

	<form action="?page=wpTrivia&module=question&action=delete_multi&quiz_id=<?php echo $this->quiz->getId(); ?>" method="post" style="display: none;" id="deleteForm">

	</form>

	<form method="get">
		<input type="hidden" name="page" value="wpTrivia">
		<input type="hidden" name="module" value="question">
		<input type="hidden" name="quiz_id" value="<?php echo $this->quiz->getId(); ?>">
	<?php
		if(!class_exists('WP_List_Table')){
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}

		$table = new WpTrivia_View_QuestionOverallTable($this->questionItems, $this->questionCount, $this->perPage);

		$table->prepare_items();

		?>
			<p class="search-box">
				<?php $table->search_box( __('Search'), 'search_id' ); ?>
			</p>
		<?php

		$table->display();
	?>
	</form>

	<?php
	}

	protected function showCopyQuestionBox() {
		?>

		<div id="wpTrivia_questionCopy_box" style="display: none;">
			<div class="wpTrivia_questionCopy">
				<form action="admin.php?page=wpTrivia&module=question&quiz_id=<?php echo $this->quiz->getId(); ?>&action=copy_question" method="POST">
					<h3 style="margin-top: 0;"><?php _e('Copy questions from another Quiz', 'wp-trivia'); ?></h3>
					<p><?php echo __('Here you can copy questions from another quiz into this quiz. (Multiple selection enabled)', 'wp-trivia'); ?></p>

					<div style="padding: 20px; display: none;" id="loadDataImg">
						<img alt="load" src="data:image/gif;base64,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">
						<?php echo __('Loading', 'wp-trivia'); ?>
					</div>

					<div style="padding: 10px;">
						<select name="copyIds[]" size="15" multiple="multiple" style="min-width: 200px; display: none;" id="questionCopySelect">
						</select>
					</div>

					<input class="button-primary" name="questionCopy" value="<?php echo __('Copy questions', 'wp-trivia'); ?>" type="submit">
				</form>
			</div>
		</div>

		<?php
	}

	protected function showSortQuestionBox() {
		?>

		<div id="wpTrivia_sortQuestion_box" style="display: none;">
			<div id="wpTrivia_sortQuestion">
				<h4>Sort questions</h4>
				<p>
					<a href="#" class="button-secondary saveQuestionSort"><?php _e( 'Save' ); ?></a>
				</p>
				<table class="widefat sortTable">
					<tbody>
					</tbody>
				</table>
				<p>
					<a href="#" class="button-secondary saveQuestionSort"><?php _e( 'Save' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}
}
