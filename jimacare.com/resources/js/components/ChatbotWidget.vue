<template>
    <div class="chatbot-container" :class="{ open: isOpen }">
        <!-- Chat Button -->
        <button class="chat-button" @click="toggleChat" :class="{ hidden: isOpen }">
            <span class="chat-icon">💬</span>
            <span class="chat-badge" v-if="unreadCount > 0">{{ unreadCount }}</span>
        </button>

        <!-- Chat Window -->
        <div class="chat-window" v-show="isOpen">
            <div class="chat-header">
                <div class="header-info">
                    <div class="bot-avatar">🤖</div>
                    <div class="header-text">
                        <h4>Jimacare Assistant</h4>
                        <span class="status">Online</span>
                    </div>
                </div>
                <button class="close-btn" @click="toggleChat">×</button>
            </div>

            <div class="chat-messages" ref="messagesContainer">
                <div 
                    v-for="(msg, index) in messages" 
                    :key="index" 
                    class="message"
                    :class="msg.sender"
                >
                    <div class="message-content">
                        <div class="message-text" v-html="formatMessage(msg.text)"></div>
                        
                        <!-- Action buttons -->
                        <div v-if="msg.data && msg.data.action === 'menu'" class="action-buttons">
                            <a 
                                v-for="option in msg.data.options" 
                                :key="option.url"
                                :href="option.url"
                                class="action-btn"
                            >
                                {{ option.text }}
                            </a>
                        </div>

                        <a 
                            v-if="msg.data && msg.data.action === 'redirect'" 
                            :href="msg.data.url"
                            class="primary-action-btn"
                        >
                            {{ msg.data.button_text }}
                        </a>

                        <!-- Service selection -->
                        <div v-if="msg.data && msg.data.action === 'select_service'" class="service-options">
                            <button 
                                v-for="option in msg.data.options"
                                :key="option.id"
                                @click="selectService(option)"
                                class="service-btn"
                            >
                                {{ option.name }}
                            </button>
                        </div>

                        <span class="message-time">{{ msg.time }}</span>
                    </div>
                </div>

                <!-- Typing indicator -->
                <div v-if="isTyping" class="message bot">
                    <div class="message-content typing">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
            </div>

            <!-- Suggestions -->
            <div class="suggestions" v-if="suggestions.length > 0">
                <button 
                    v-for="suggestion in suggestions" 
                    :key="suggestion"
                    @click="sendSuggestion(suggestion)"
                    class="suggestion-btn"
                >
                    {{ suggestion }}
                </button>
            </div>

            <!-- Input Area -->
            <div class="chat-input">
                <input 
                    type="text" 
                    v-model="inputMessage"
                    @keyup.enter="sendMessage"
                    placeholder="Type your message..."
                    :disabled="isTyping"
                >
                <button @click="sendMessage" :disabled="!inputMessage.trim() || isTyping">
                    <svg viewBox="0 0 24 24" width="24" height="24">
                        <path fill="currentColor" d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ChatbotWidget',

    data() {
        return {
            isOpen: false,
            messages: [],
            inputMessage: '',
            isTyping: false,
            suggestions: [],
            unreadCount: 0,
            context: {}
        };
    },

    mounted() {
        this.initChat();
    },

    methods: {
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.unreadCount = 0;
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            }
        },

        async initChat() {
            try {
                const response = await axios.get('/api/v1/chatbot/init');
                this.addBotMessage(response.data.greeting);
                this.suggestions = response.data.quick_actions.map(a => a.text);
            } catch (error) {
                this.addBotMessage("Hello! 👋 How can I help you today?");
                this.suggestions = ['Find a carer', 'Post a job', 'How it works'];
            }
        },

        async sendMessage() {
            const message = this.inputMessage.trim();
            if (!message) return;

            this.addUserMessage(message);
            this.inputMessage = '';
            this.suggestions = [];
            this.isTyping = true;

            try {
                const response = await axios.post('/api/v1/chatbot/message', {
                    message: message,
                    context: this.context
                });

                this.isTyping = false;
                this.addBotMessage(
                    response.data.response, 
                    response.data.data
                );
                
                if (response.data.suggestions) {
                    this.suggestions = response.data.suggestions;
                }
            } catch (error) {
                this.isTyping = false;
                this.addBotMessage("Sorry, I'm having trouble connecting. Please try again.");
            }
        },

        sendSuggestion(suggestion) {
            this.inputMessage = suggestion;
            this.sendMessage();
        },

        selectService(option) {
            window.location.href = option.url;
        },

        addUserMessage(text) {
            this.messages.push({
                sender: 'user',
                text: text,
                time: this.formatTime(new Date())
            });
            this.scrollToBottom();
        },

        addBotMessage(text, data = null) {
            this.messages.push({
                sender: 'bot',
                text: text,
                data: data,
                time: this.formatTime(new Date())
            });
            
            if (!this.isOpen) {
                this.unreadCount++;
            }
            
            this.scrollToBottom();
        },

        formatMessage(text) {
            // Convert markdown-like syntax to HTML
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\n/g, '<br>');
        },

        formatTime(date) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
    }
};
</script>

<style scoped>
.chatbot-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.chat-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
}

.chat-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
}

.chat-button.hidden {
    display: none;
}

.chat-icon {
    font-size: 28px;
}

.chat-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-window {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 380px;
    height: 550px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.bot-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.header-text h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.header-text .status {
    font-size: 12px;
    opacity: 0.9;
}

.close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.close-btn:hover {
    opacity: 1;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.message {
    display: flex;
    max-width: 85%;
}

.message.user {
    align-self: flex-end;
}

.message.bot {
    align-self: flex-start;
}

.message-content {
    padding: 12px 16px;
    border-radius: 18px;
    position: relative;
}

.message.user .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.message.bot .message-content {
    background: #f3f4f6;
    color: #1f2937;
    border-bottom-left-radius: 4px;
}

.message-text {
    font-size: 14px;
    line-height: 1.5;
}

.message-time {
    font-size: 10px;
    opacity: 0.6;
    margin-top: 4px;
    display: block;
}

.typing {
    display: flex;
    gap: 4px;
    padding: 16px 20px;
}

.typing .dot {
    width: 8px;
    height: 8px;
    background: #9ca3af;
    border-radius: 50%;
    animation: bounce 1.4s infinite ease-in-out;
}

.typing .dot:nth-child(1) { animation-delay: -0.32s; }
.typing .dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 12px;
}

.action-btn {
    display: block;
    padding: 10px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #4b5563;
    text-decoration: none;
    font-size: 13px;
    text-align: center;
    transition: all 0.2s;
}

.action-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.primary-action-btn {
    display: block;
    padding: 12px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
    margin-top: 12px;
    transition: transform 0.2s;
}

.primary-action-btn:hover {
    transform: translateY(-2px);
}

.service-options {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
}

.service-btn {
    padding: 8px 16px;
    background: white;
    border: 1px solid #667eea;
    border-radius: 20px;
    color: #667eea;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.service-btn:hover {
    background: #667eea;
    color: white;
}

.suggestions {
    padding: 8px 16px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    border-top: 1px solid #e5e7eb;
}

.suggestion-btn {
    padding: 6px 12px;
    background: #f3f4f6;
    border: none;
    border-radius: 16px;
    font-size: 12px;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.2s;
}

.suggestion-btn:hover {
    background: #667eea;
    color: white;
}

.chat-input {
    display: flex;
    padding: 12px;
    border-top: 1px solid #e5e7eb;
    gap: 8px;
}

.chat-input input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

.chat-input input:focus {
    border-color: #667eea;
}

.chat-input button {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.chat-input button:hover:not(:disabled) {
    transform: scale(1.05);
}

.chat-input button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 480px) {
    .chat-window {
        width: 100vw;
        height: 100vh;
        bottom: 0;
        right: 0;
        border-radius: 0;
    }
}
</style>

