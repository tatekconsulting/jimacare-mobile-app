<template>
    <div class="push-notification-widget">
        <div class="notification-header">
            <div class="header-icon">
                <i class="fas fa-bell"></i>
            </div>
            <div class="header-content">
                <h4>Push Notifications</h4>
                <p>Get instant alerts for messages and bookings</p>
            </div>
        </div>

        <div class="notification-status" :class="{ enabled: isSubscribed }">
            <span v-if="!supported" class="not-supported">
                <i class="fas fa-exclamation-triangle"></i>
                Push notifications not supported in this browser
            </span>
            
            <template v-else>
                <div class="status-info">
                    <span class="status-badge" :class="{ active: isSubscribed }">
                        {{ isSubscribed ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
                
                <button 
                    @click="toggleSubscription" 
                    :disabled="loading"
                    class="toggle-btn"
                    :class="{ subscribed: isSubscribed }"
                >
                    <span v-if="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                    <span v-else>
                        {{ isSubscribed ? 'Disable' : 'Enable' }}
                    </span>
                </button>
            </template>
        </div>

        <div v-if="isSubscribed" class="notification-options">
            <h5>Notify me about:</h5>
            <label class="option-item">
                <input type="checkbox" v-model="options.messages" @change="saveOptions">
                <span>New messages</span>
            </label>
            <label class="option-item">
                <input type="checkbox" v-model="options.bookings" @change="saveOptions">
                <span>Booking updates</span>
            </label>
            <label class="option-item">
                <input type="checkbox" v-model="options.jobs" @change="saveOptions">
                <span>New jobs in my area</span>
            </label>
            <label class="option-item">
                <input type="checkbox" v-model="options.availability" @change="saveOptions">
                <span>Carers available nearby</span>
            </label>
        </div>

        <div v-if="isSubscribed" class="test-notification">
            <button @click="sendTestNotification" :disabled="testLoading" class="test-btn">
                <i class="fas fa-paper-plane"></i>
                Send Test Notification
            </button>
        </div>

        <div v-if="message" class="feedback-message" :class="messageType">
            {{ message }}
        </div>
    </div>
</template>

<script>
export default {
    name: 'PushNotificationToggle',

    props: {
        vapidPublicKey: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            supported: false,
            isSubscribed: false,
            loading: false,
            testLoading: false,
            message: '',
            messageType: 'success',
            options: {
                messages: true,
                bookings: true,
                jobs: true,
                availability: false
            }
        };
    },

    mounted() {
        this.checkSupport();
        this.checkSubscription();
        this.loadOptions();
    },

    methods: {
        checkSupport() {
            this.supported = 'serviceWorker' in navigator && 'PushManager' in window;
        },

        async checkSubscription() {
            if (!this.supported) return;

            try {
                const registration = await navigator.serviceWorker.ready;
                const subscription = await registration.pushManager.getSubscription();
                this.isSubscribed = !!subscription;
            } catch (error) {
                console.error('Error checking subscription:', error);
            }
        },

        async toggleSubscription() {
            if (this.isSubscribed) {
                await this.unsubscribe();
            } else {
                await this.subscribe();
            }
        },

        async subscribe() {
            this.loading = true;
            this.message = '';

            try {
                // Request notification permission
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    this.message = 'Notification permission denied';
                    this.messageType = 'error';
                    return;
                }

                // Get service worker registration
                const registration = await navigator.serviceWorker.ready;

                // Subscribe to push
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
                });

                // Send subscription to server
                await axios.post('/api/v1/push/subscribe', subscription.toJSON());

                this.isSubscribed = true;
                this.message = 'Push notifications enabled!';
                this.messageType = 'success';

            } catch (error) {
                console.error('Subscription error:', error);
                this.message = error.response?.data?.message || 'Failed to enable notifications';
                this.messageType = 'error';
            } finally {
                this.loading = false;
            }
        },

        async unsubscribe() {
            this.loading = true;
            this.message = '';

            try {
                const registration = await navigator.serviceWorker.ready;
                const subscription = await registration.pushManager.getSubscription();

                if (subscription) {
                    await subscription.unsubscribe();
                }

                await axios.post('/api/v1/push/unsubscribe');

                this.isSubscribed = false;
                this.message = 'Push notifications disabled';
                this.messageType = 'info';

            } catch (error) {
                console.error('Unsubscribe error:', error);
                this.message = 'Failed to disable notifications';
                this.messageType = 'error';
            } finally {
                this.loading = false;
            }
        },

        async sendTestNotification() {
            this.testLoading = true;
            
            try {
                await axios.post('/api/v1/push/test');
                this.message = 'Test notification sent! Check your notifications.';
                this.messageType = 'success';
            } catch (error) {
                this.message = error.response?.data?.message || 'Failed to send test';
                this.messageType = 'error';
            } finally {
                this.testLoading = false;
            }
        },

        saveOptions() {
            localStorage.setItem('pushNotificationOptions', JSON.stringify(this.options));
        },

        loadOptions() {
            const saved = localStorage.getItem('pushNotificationOptions');
            if (saved) {
                this.options = { ...this.options, ...JSON.parse(saved) };
            }
        },

        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    }
};
</script>

<style scoped>
.push-notification-widget {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.notification-header {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.header-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.header-content h4 {
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

.header-content p {
    margin: 0;
    font-size: 13px;
    color: #6b7280;
}

.notification-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}

.not-supported {
    color: #dc2626;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    background: #e5e7eb;
    color: #6b7280;
}

.status-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.toggle-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.toggle-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.toggle-btn.subscribed {
    background: #e5e7eb;
    color: #6b7280;
}

.toggle-btn.subscribed:hover {
    background: #fee2e2;
    color: #dc2626;
    box-shadow: none;
}

.toggle-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.notification-options {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.notification-options h5 {
    margin: 0 0 15px 0;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    cursor: pointer;
}

.option-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #667eea;
}

.option-item span {
    font-size: 14px;
    color: #4b5563;
}

.test-notification {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.test-btn {
    width: 100%;
    padding: 12px;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    background: transparent;
    color: #6b7280;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.test-btn:hover {
    border-color: #667eea;
    color: #667eea;
}

.test-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.feedback-message {
    margin-top: 15px;
    padding: 12px;
    border-radius: 8px;
    font-size: 14px;
    text-align: center;
}

.feedback-message.success {
    background: #d1fae5;
    color: #065f46;
}

.feedback-message.error {
    background: #fee2e2;
    color: #991b1b;
}

.feedback-message.info {
    background: #dbeafe;
    color: #1e40af;
}
</style>

