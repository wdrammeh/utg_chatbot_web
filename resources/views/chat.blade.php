<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTG FAQ Chatbot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        #chat-container {
            flex: 1;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        /* Message Panels */
        .msg-panel {
            max-width: 85%;
            padding: 0.85rem 1.25rem;
            margin-bottom: 1rem;
            line-height: 1.5;
            font-style: normal !important;
            /* Force no italics */
        }

        .user-panel {
            background-color: #f0f4f9;
            color: #1f1f1f;
            border-radius: 1.25rem 1.25rem 0.25rem 1.25rem;
            align-self: flex-end;
        }

        .bot-panel {
            background-color: #ffffff;
            color: #1f1f1f;
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem 1.25rem 1.25rem 0.25rem;
            align-self: flex-start;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .error-panel {
            border-color: #fca5a5;
            background-color: #fef2f2;
            color: #b91c1c;
        }

        /* Gemini-style Loading State */
        .loading-wrapper {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            margin-bottom: 1rem;
            width: 100%;
        }

        .sparkle-icon {
            color: #4285f4;
            animation: geminiRotate 2s infinite linear, geminiPulse 1.5s infinite ease-in-out;
            filter: drop-shadow(0 0 4px rgba(66, 133, 244, 0.4));
        }

        @keyframes geminiRotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes geminiPulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.2) rotate(180deg);
            }
        }

        /* Skeleton Bars Commented Out as requested */
        /*
        .gemini-loader {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 60%;
        }
        .shimmer {
            height: 14px;
            background: linear-gradient(90deg, #f0f4f9 25%, #d1e3fa 50%, #f0f4f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
            border-radius: 4px;
        }
        */
    </style>
</head>

<body class="text-[#1f1f1f]">

    <div id="chat-container" class="flex flex-col">
        <div class="max-w-3xl w-full mx-auto px-6 pt-12 pb-32 flex flex-col">
            <!-- Welcome Header (Gemini Style) -->
            <div id="welcome-screen" class="welcome-badge flex flex-col items-center mb-16">
                <div class="mb-6 p-4 rounded-2xl bg-white shadow-sm border border-gray-100">
                    <!-- Placeholder for UTG Badge -->
                    <img src="{{ asset('utg-logo.gif') }}" alt="UTG" class="w-14 h-14 object-contain">
                </div>
                <h1 class="text-3xl md:text-4xl font-medium text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-red-400 pb-2">
                    Hello, UTG FAQ Chatbot here!
                </h1>
                <p class="text-gray-500 mt-4 text-center max-w-md">
                    How can I help you today? Ask me about courses, admissions, or campus services
                </p>
            </div>

            <!-- Chat Content (Panels) -->
            <div id="chat-content" class="flex flex-col w-full"></div>

            <!-- Loading Indicator -->
            <div id="loading" class="hidden">
                <div class="loading-wrapper">
                    <div class="sparkle-icon">
                        <!-- Gemini-style Sparkle SVG -->
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z" />
                        </svg>
                    </div>
                    <!-- Skeletons Commented Out -->
                    <!-- 
                    <div class="gemini-loader ml-3">
                        <div class="shimmer w-full"></div>
                    </div> 
                    -->
                </div>
            </div>
        </div>
    </div>

    <!-- Input Bar -->
    <div class="w-full bg-gradient-to-t from-white via-white to-transparent pb-6 pt-10 px-4 fixed bottom-0 left-0">
        <div class="max-w-3xl mx-auto">
            <div class="relative flex items-center bg-[#f0f4f9] rounded-full focus-within:bg-white focus-within:ring-1 focus-within:ring-gray-300 border border-transparent transition-all shadow-sm">
                <input id="question" type="text" placeholder="Type your question..." class="flex-1 bg-transparent px-6 py-4 focus:outline-none text-gray-700">
                <button id="sendBtn" class="p-2 mr-3 text-blue-600 disabled:text-gray-400 transition-colors" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                        <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                    </svg>
                </button>
            </div>
            <p class="text-[12px] text-gray-500 text-center mt-3 font-light">
                Please verify important information.
            </p>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const $chatContainer = $('#chat-container');
            const $chatContent = $('#chat-content');
            const $input = $('#question');
            const $loading = $('#loading');
            const $sendBtn = $('#sendBtn');
            const $welcome = $('#welcome-screen');

            $input.on('input', function() {
                $sendBtn.prop('disabled', this.value.trim() === '');
            });

            function appendMessage(role, text, isError = false) {
                if ($welcome.is(':visible')) $welcome.fadeOut(200);

                const isUser = role === 'user';
                const panelClass = isUser ? 'user-panel' : (isError ? 'bot-panel error-panel' : 'bot-panel');

                const msgHtml = `
                    <div class="msg-panel ${panelClass}">
                        ${text}
                    </div>
                `;

                $chatContent.append(msgHtml);
                $chatContainer.animate({
                    scrollTop: $chatContainer.prop("scrollHeight")
                }, 300);
            }

            function sendMessage() {
                const question = $input.val().trim();
                if (!question) return;

                $input.val('').trigger('input');
                appendMessage('user', question);
                $loading.removeClass('hidden');
                $chatContainer.animate({
                    scrollTop: $chatContainer.prop("scrollHeight")
                }, 300);

                $.ajax({
                    url: "http://127.0.0.1:5000/ask",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({
                        question: question
                    }),
                    timeout: 10000,
                    success: function(data) {
                        $loading.addClass('hidden');
                        appendMessage('bot', data.answer || "I'm sorry, I don't have information on that topic.");
                    },
                    error: function(xhr, status, error) {
                        $loading.addClass('hidden');
                        const errorMsg = "I'm having trouble connecting to the server. Please try again later.";
                        appendMessage('bot', errorMsg, true);
                    }
                });
            }

            $sendBtn.on('click', sendMessage);
            $input.on('keypress', function(e) {
                if (e.which === 13 && !$(this).prop('disabled')) sendMessage();
            });
        });
    </script>
</body>

</html>