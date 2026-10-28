<dialog id="quiz-dialog" class="quiz-dialog" aria-labelledby="quiz-title">
	<header class="quiz-header">
		<h2 id="quiz-title">Find your match</h2>
		<button type="button" class="quiz-close" aria-label="Close quiz">
			<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
		</button>
	</header>

	<div class="quiz-body">
		<div class="quiz-log" role="log" aria-live="polite"></div>
	</div>

	<footer class="quiz-footer">
		<div class="quiz-choices" role="group" aria-label="Answers"></div>

		<form class="quiz-email" hidden novalidate>
			<label for="quiz-email-input" class="quiz-email-label">Email me my results <span>+ get 10% off with GLAM10</span></label>
			<div class="quiz-email-row">
				<input type="email" id="quiz-email-input" name="email" placeholder="janedoe@gmail.com" autocomplete="email" required>
				<button type="submit" class="quiz-send" aria-label="Send my results">
					<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M4 12L20 4l-6 16-3-7-7-1z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
				</button>
			</div>
			<p class="quiz-email-consent">You'll also join the glam newsletter. Unsubscribe anytime.</p>
			<p class="quiz-email-status" role="status" aria-live="polite"></p>
		</form>

		<button type="button" class="quiz-restart" hidden>Start over</button>
	</footer>
</dialog>
