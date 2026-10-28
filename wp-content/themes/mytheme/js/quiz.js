/**
 * Product quiz on the shop page: chat-style questions → starter kit from the server.
 * Questions, AJAX URL and nonce come from wp_localize_script (window.glamQuiz).
 */
document.addEventListener('DOMContentLoaded', function () {
	var data = window.glamQuiz;
	var dialog = document.getElementById('quiz-dialog');
	if (!data || !dialog || typeof dialog.showModal !== 'function') {
		return;
	}

	var log = dialog.querySelector('.quiz-log');
	var body = dialog.querySelector('.quiz-body');
	var choices = dialog.querySelector('.quiz-choices');
	var emailForm = dialog.querySelector('.quiz-email');
	var emailInput = emailForm.querySelector('input');
	var emailStatus = emailForm.querySelector('.quiz-email-status');
	var restart = dialog.querySelector('.quiz-restart');
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var delay = reduceMotion ? 0 : 600;
	var answers = {};
	var step = 0;
	var opener = null;

	function scrollToEnd() {
		body.scrollTo({ top: body.scrollHeight, behavior: reduceMotion ? 'auto' : 'smooth' });
	}

	function avatar() {
		var span = document.createElement('span');
		span.className = 'quiz-avatar';
		span.setAttribute('aria-hidden', 'true');
		span.innerHTML = '<svg viewBox="0 0 40 40" width="40" height="40"><circle cx="20" cy="15" r="7" fill="currentColor"/><path d="M6 36c1.5-8 7-12 14-12s12.5 4 14 12" fill="currentColor"/></svg>';
		return span;
	}

	function addMessage(who, text) {
		var row = document.createElement('div');
		row.className = 'quiz-msg quiz-msg--' + who;
		var bubble = document.createElement('p');
		bubble.className = 'quiz-bubble';
		bubble.textContent = text;
		var label = document.createElement('span');
		label.className = 'screen-reader-text';
		label.textContent = who === 'bot' ? 'glam: ' : 'You: ';
		bubble.prepend(label);
		row.append(avatar(), bubble);
		log.appendChild(row);
		scrollToEnd();
		return row;
	}

	// "glam is typing…" bubble while waiting.
	function typing(then) {
		if (!delay) {
			then();
			return;
		}
		var row = addMessage('bot', '');
		row.classList.add('quiz-msg--typing');
		row.querySelector('.quiz-bubble').innerHTML = '<span></span><span></span><span></span>';
		setTimeout(function () {
			row.remove();
			then();
		}, delay);
	}

	function ask() {
		var question = data.questions[step];
		typing(function () {
			addMessage('bot', question.text);
			choices.innerHTML = '';
			question.options.forEach(function (option) {
				var button = document.createElement('button');
				button.type = 'button';
				button.className = 'quiz-choice';
				button.textContent = option.label;
				button.addEventListener('click', function () {
					answers[question.key] = option.value;
					choices.innerHTML = '';
					addMessage('user', option.label);
					step++;
					if (step < data.questions.length) {
						ask();
					} else {
						finish();
					}
				});
				choices.appendChild(button);
			});
			choices.querySelector('button').focus();
		});
	}

	function request(action, extra) {
		var form = new FormData();
		form.append('action', action);
		form.append('nonce', data.nonce);
		Object.keys(answers).forEach(function (key) {
			form.append(key, answers[key]);
		});
		Object.keys(extra || {}).forEach(function (key) {
			form.append(key, extra[key]);
		});
		return fetch(data.ajaxUrl, { method: 'POST', body: form }).then(function (res) {
			return res.json();
		});
	}

	function finish() {
		typing(function () {
			addMessage('bot', data.finalText);
			request('mytheme_quiz_results')
				.then(function (response) {
					if (!response.success) {
						addMessage('bot', response.data.message);
						return;
					}
					var results = document.createElement('div');
					results.className = 'quiz-results';
					results.tabIndex = -1;
					results.innerHTML = response.data.html;
					log.appendChild(results);
					emailForm.hidden = false;
					// Keyboard and screen reader users land on the kit, not back at the top of the page.
					results.focus({ preventScroll: true });
				})
				.catch(function () {
					addMessage('bot', 'Something went wrong. Please try again.');
				})
				.finally(function () {
					restart.hidden = false;
					scrollToEnd();
				});
		});
	}

	function start() {
		answers = {};
		step = 0;
		log.innerHTML = '';
		choices.innerHTML = '';
		emailForm.hidden = true;
		emailForm.reset();
		emailStatus.textContent = '';
		restart.hidden = true;
		ask();
	}

	document.querySelectorAll('[data-quiz-open]').forEach(function (button) {
		button.addEventListener('click', function () {
			opener = button;
			dialog.showModal();
			if (!log.children.length) {
				start();
			}
		});
	});

	dialog.querySelector('.quiz-close').addEventListener('click', function () {
		dialog.close();
	});
	dialog.addEventListener('click', function (e) {
		if (e.target !== dialog) {
			return;
		}
		var r = dialog.getBoundingClientRect();
		if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) {
			dialog.close();
		}
	});

	// Return focus to the button that opened the quiz (Safari doesn't focus buttons on click).
	dialog.addEventListener('close', function () {
		if (opener) {
			opener.focus();
		}
	});

	restart.addEventListener('click', start);

	emailForm.addEventListener('submit', function (e) {
		e.preventDefault();
		if (!emailInput.checkValidity()) {
			emailInput.setAttribute('aria-invalid', 'true');
			emailStatus.textContent = 'Please enter a valid email address.';
			emailInput.focus();
			return;
		}
		emailInput.removeAttribute('aria-invalid');
		emailStatus.textContent = 'Sending...';
		request('mytheme_quiz_email', { email: emailInput.value })
			.then(function (response) {
				emailStatus.textContent = response.data.message;
			})
			.catch(function () {
				emailStatus.textContent = 'Something went wrong. Please try again.';
			});
	});
});
