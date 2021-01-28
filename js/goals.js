$(document).ready(function(){
	// replace it! window.yaCounter01234567
	function reach(e){if(typeof window.yaCounter01234567!='undefined'){window.yaCounter01234567.reachGoal(e.data.goal);}}
	function bind(exp,goal,evt){if(typeof evt=='undefined'){evt='click';}$(document).on(evt,exp,{goal:goal},reach);}
	// bind(jquerySelector, goal, eventName), ex:
	// bind('.btn', 'btn_ok');
	// bind('form', 'form_ok', 'submit');
	// bind('form[id^="feedback-callback-form"]', 'callback_ok', 'cms.feedback.sended');
});
