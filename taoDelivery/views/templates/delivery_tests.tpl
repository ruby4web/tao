<div id="test-container" class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select delivery tests')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="test-tree"></div>
		<div class="breaker"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-test" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<div id="test-order-container" class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Tests sequence')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="item-list">
			<span class="elt-info"<?php if (!count(get_data('testSequence'))) echo ' style="display:none"' ?>><?=__('Drag and drop the tests to order them')?></span>
			<ul id="test-sequence" class="sortable-list">
			<?foreach(get_data('testSequence') as $index => $test):?>
				<li class="ui-state-default" id="test_<?=$test['uri']?>" >
					<span class='ui-icon ui-icon-arrowthick-2-n-s' ></span>
					<span class="ui-icon ui-icon-grip-dotted-vertical" ></span>
					<?=$index?>. <?=$test['label']?>
				</li>
			<?endforeach?>
			</ul>
		</div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-test-sequence" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<?if(!get_data('myForm')):?>
	<input type='hidden' name='uri' value="<?=get_data('uri')?>" />
	<input type='hidden' name='classUri' value="<?=get_data('classUri')?>" />
<?endif?>
<script type="text/javascript">
$(document).ready(function(){
	var sequence = <?=get_data('relatedTests')?>;
	var labels = <?=get_data('allTests')?>;

	function buildTestList(id, tests, labels){
		html = '';
		for (i in tests) {
			testId = tests[i];
			html += "<li class='ui-state-default' id='" + testId + "' >";
			html += "<span class='ui-icon ui-icon-arrowthick-2-n-s' /><span class='ui-icon ui-icon-grip-dotted-vertical' />";
			html += i + ". " + labels[testId];
			html += "</li>";
		}
		$("#" + id).html(html);
	}

	if (ctx_extension) {
		url = root_url + ctx_extension + '/' + ctx_module + '/';
	}

	require(['require', 'jquery', 'generis.tree.select'], function(req, $, GenerisTreeSelectClass) {
		new GenerisTreeSelectClass('#test-tree', root_url + 'tao/GenerisTree/getData', {
			actionId: 'test',
			saveUrl : url + 'saveTests',
			saveCallback: function (data){
				if (buildTestList != undefined) {
					newSequence = {};
					sequence = {};
					for(attr in data){
						if(/^instance_/.test(attr)){
							newSequence[parseInt(attr.replace('instance_', ''))+1] = 'test_'+ data[attr];
							sequence[parseInt(attr.replace('instance_', ''))+1] = data[attr];
						}
					}
					buildTestList("test-sequence", newSequence, labels);
					if ($('#test-sequence li').length) $('#test-sequence').prev('.elt-info').show();
				}
			},
			serverParameters: {
				openNodes: <?=json_encode(get_data('testOpenNodes'))?>,
				rootNode: <?=json_encode(get_data('testRootNode'))?>
			},
			checkedNodes : sequence
		});
	});

	$("#test-sequence").sortable({
		axis: 'y',
		opacity: 0.6,
		placeholder: 'ui-state-error',
		tolerance: 'pointer',
		update: function(event, ui){
			listTests = $(this).sortable('toArray');
			newSequence = {};
			sequence = {};
			for (i = 0; i < listTests.length; i++){
				index = i+1;
				newSequence[index] = listTests[i];
				sequence[index] = listTests[i].replace('test_', '');
			}
			buildTestList('test-sequence', newSequence, labels);
		}
	});

	$("#test-sequence li").bind('mousedown', function(){
		$(this).css('cursor', 'move');
	});
	$("#test-sequence li").bind('mouseup',function(){
		$(this).css('cursor', 'pointer');
	});

	$("#saver-action-test-sequence").click(function(){
		toSend = {};
		for(index in sequence){
			toSend['instance_'+index] = sequence[index];
		}
		toSend.uri = $("input[name=uri]").val();
		toSend.classUri = $("input[name=classUri]").val();
		$.ajax({
			url: url + 'saveTests',
			type: "POST",
			data: toSend,
			dataType: 'json',
			success: function(response){
				if (response.saved) {
					helpers.createInfoMessage("<?=__('Sequence saved successfully')?>");
				}
			},
			complete: function(){
				helpers.loaded();
			}
		});
	});
});
</script>
