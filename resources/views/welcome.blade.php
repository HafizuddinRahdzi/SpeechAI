<style>
    body {
        background: linear-gradient(135deg, #c2e9fb 0%, #a1c4fd 100%);
        min-height: 100vh;
        font-family: "Poppins", sans-serif;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border-radius: 10px;
        border: 2px solid #e0e6ed;
        background-color: #f9fafc;
        font-size: 16px;
        font-family: "Poppins", sans-serif;
        color: #333;
        outline: none;
        transition: all 0.3s ease;
    }

    /* On focus */
    .form-control:focus {
        border-color: #4e9af1;
        box-shadow: 0 0 0 4px rgba(78, 154, 241, 0.2);
        background-color: #fff;
    }

    /* Placeholder style */
    .form-control::placeholder {
        color: #aaa;
        font-style: italic;
    }

    /* Label style */
    .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .reading-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 700px;
        margin: 40px auto;
        text-align: center;
        transition: all 0.3s ease;
    }

    .reading-card:hover {
        transform: translateY(-5px);
    }

    h3 {
        color: #333;
        font-weight: 700;
        margin-bottom: 20px;
    }

    #text {
        font-size: 18px;
        color: #444;
        margin-bottom: 20px;
        line-height: 1.6;
        background: #f9fafc;
        border-radius: 10px;
        padding: 15px;
    }

    #start-btn, #stop-btn {
        font-size: 18px;
        border-radius: 30px;
        padding: 10px 25px;
        margin: 10px;
        transition: all 0.3s;
    }

    #start-btn {
        background-color: #28a745;
        color: white;
    }

    #start-btn:hover {
        background-color: #218838;
    }

    #stop-btn {
        background-color: #dc3545;
        color: white;
    }

    #stop-btn:hover {
        background-color: #c82333;
    }

    .alert {
        border-radius: 12px;
        font-size: 16px;
        margin-top: 25px;
    }

    .mic-icon {
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.7; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); opacity: 0.7; }
    }

    /* Hide recognized speech section */
    #recognized-section {
        display: none;
    }

    /* AI typing indicator */
    .ai-typing {
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 16px;
        color: #666;
        margin-top: 15px;
    }

    .ai-typing span {
        display: inline-block;
        margin: 0 2px;
        width: 8px;
        height: 8px;
        background-color: #666;
        border-radius: 50%;
        animation: blink 1.4s infinite both;
    }

    .ai-typing span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .ai-typing span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes blink {
        0%, 80%, 100% { opacity: 0; }
        40% { opacity: 1; }
    }

    /* Simulated AI feedback text */
    #ai-feedback {
        color: #444;
        font-size: 16px;
        text-align: left;
        line-height: 1.6;
    }

    #ai-feedback span {
        color: gray;
        transition: color 0.3s ease;
    }

    /* Submit Button */
    #submit-btn {
        background: linear-gradient(135deg, #4e9af1 0%, #1d72b8 100%);
        color: #fff;
        border: none;
        border-radius: 30px;
        padding: 12px 25px;
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(78, 154, 241, 0.3);
    }

    /* Hover effect */
    #submit-btn:hover {
        background: linear-gradient(135deg, #1d72b8 0%, #155d97 100%);
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(29, 114, 184, 0.4);
    }

    /* Active (clicked) effect */
    #submit-btn:active {
        transform: translateY(0);
        box-shadow: 0 3px 8px rgba(29, 114, 184, 0.3);
    }

    /* Disabled state */
    #submit-btn:disabled {
        background: #bfc8d6;
        color: #eee;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* Add a small animation when it first becomes enabled */
    #submit-btn:not(:disabled) {
        animation: fadeInBtn 0.6s ease;
    }

    @keyframes fadeInBtn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

</style>

<div class="reading-card">
    <h3>📖 {{ $passage->title }}</h3>
    <p id="text">{{ $passage->content }}</p>

    <form id="feedback-form" method="POST" action="{{ route('feedback.store') }}">
        @csrf
        <div class="mb-3 text-start">
            <label for="student_name" class="form-label fw-bold">👩‍🎓 Student Name:</label>
            <input type="text" name="student_name" id="student_name" class="form-control" placeholder="Enter your name" required>
        </div>

        <div class="mb-3">
            <button type="button" id="start-btn"><span id="mic-icon">🎙</span> Start Reading</button>
            <button type="button" id="stop-btn" disabled>⏹ Stop</button>
        </div>

        <!-- Recognized Speech (Hidden) -->
        <div id="recognized-section">
            <p><strong>Recognized Speech:</strong></p>
            <p id="result"></p>
        </div>

        <input type="hidden" name="passage_id" value="{{ $passage->id }}">
        <input type="hidden" name="spoken_text" id="spoken_text">

        <button type="submit" class="btn btn-primary w-100 mt-3" id="submit-btn" disabled>
            📤 Submit for Feedback
        </button>
    </form>

    <!-- AI feedback output -->
    @if (session('success'))
        <div class="alert alert-success mt-4 text-start">
            <h5>🤖 AI Pronunciation Feedback</h5>

            <!-- Typing indicator -->
            <div class="ai-typing" id="ai-typing">
                <span></span><span></span><span></span>
            </div>

            <!-- The white-space rule ensures line breaks are rendered -->
            <div id="ai-feedback" class="mt-3" style="white-space: pre-line;"></div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const aiTyping = document.getElementById('ai-typing');
                    const feedbackContainer = document.getElementById('ai-feedback');

                    // Fix: Use <br> for line breaks, not \n
                    const fullText = `{!! nl2br(e(session('success'))) !!}`; 

                    // Split by spaces but preserve <br> tags as HTML line breaks
                    const words = fullText.split(/(\s+|<br\s*\/?>)/i);

                    setTimeout(() => {
                        aiTyping.style.display = 'none'; // hide typing dots
                        feedbackContainer.innerHTML = ''; // clear before showing

                        words.forEach((word, index) => {
                            if (word.match(/<br\s*\/?>/i)) {
                                // Create an actual <br> element
                                feedbackContainer.appendChild(document.createElement('br'));
                            } else {
                                const span = document.createElement('span');
                                span.textContent = word;
                                feedbackContainer.appendChild(span);

                                // Delay for typing animation
                                setTimeout(() => {
                                    span.style.color = 'black';
                                }, index * 80);
                            }
                        });
                    }, 2000);
                });
            </script>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mt-3">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif
</div>

<script>
const startBtn = document.getElementById('start-btn');
const stopBtn = document.getElementById('stop-btn');
const resultEl = document.getElementById('result');
const spokenInput = document.getElementById('spoken_text');
const submitBtn = document.getElementById('submit-btn');
const micIcon = document.getElementById('mic-icon');

let recognition = new(window.SpeechRecognition || window.webkitSpeechRecognition)();
recognition.lang = 'en-US';
recognition.interimResults = false;
recognition.continuous = true;

startBtn.onclick = () => {
    recognition.start();
    startBtn.disabled = true;
    stopBtn.disabled = false;
    micIcon.classList.add('mic-icon');
};

stopBtn.onclick = () => {
    recognition.stop();
    startBtn.disabled = false;
    stopBtn.disabled = true;
    micIcon.classList.remove('mic-icon');
};

recognition.onresult = (event) => {
    let text = Array.from(event.results)
        .map(result => result[0].transcript)
        .join(' ');
    resultEl.textContent = text; // still stores recognized speech (hidden)
    spokenInput.value = text;
    submitBtn.disabled = false;
};
</script>