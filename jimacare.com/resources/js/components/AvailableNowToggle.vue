<template>
    <div class="available-now-widget">
        <div class="toggle-container" :class="{ active: isAvailable }">
            <div class="toggle-header">
                <div class="status-indicator" :class="{ online: isAvailable }"></div>
                <span class="status-text">{{ isAvailable ? 'Available Now' : 'Not Available' }}</span>
            </div>
            
            <label class="toggle-switch">
                <input 
                    type="checkbox" 
                    v-model="isAvailable" 
                    @change="toggleAvailability"
                    :disabled="loading"
                >
                <span class="slider"></span>
            </label>
        </div>

        <div v-if="isAvailable" class="availability-details">
            <div class="time-remaining">
                <i class="fas fa-clock"></i>
                <span>{{ timeRemaining }}</span>
            </div>
            
            <select v-model="duration" @change="updateDuration" class="duration-select">
                <option value="1">1 hour</option>
                <option value="2">2 hours</option>
                <option value="4">4 hours</option>
                <option value="8">8 hours</option>
                <option value="12">12 hours</option>
                <option value="24">24 hours</option>
            </select>
        </div>

        <div v-if="message" class="feedback-message" :class="messageType">
            {{ message }}
        </div>
    </div>
</template>

<script>
export default {
    name: 'AvailableNowToggle',
    
    data() {
        return {
            isAvailable: false,
            availableUntil: null,
            duration: 4,
            loading: false,
            message: '',
            messageType: 'success',
            timer: null
        };
    },

    computed: {
        timeRemaining() {
            if (!this.availableUntil) return '';
            
            const now = new Date();
            const until = new Date(this.availableUntil);
            const diff = until - now;
            
            if (diff <= 0) return 'Expired';
            
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            if (hours > 0) {
                return `${hours}h ${minutes}m remaining`;
            }
            return `${minutes}m remaining`;
        }
    },

    mounted() {
        this.fetchStatus();
        this.startTimer();
    },

    beforeDestroy() {
        if (this.timer) {
            clearInterval(this.timer);
        }
    },

    methods: {
        async fetchStatus() {
            try {
                const response = await axios.get('/api/v1/availability/status');
                this.isAvailable = response.data.available_now;
                this.availableUntil = response.data.available_until;
            } catch (error) {
                console.error('Error fetching availability status:', error);
            }
        },

        async toggleAvailability() {
            this.loading = true;
            this.message = '';

            try {
                const response = await axios.post('/api/v1/availability/toggle', {
                    available: this.isAvailable,
                    duration: this.duration
                });

                this.availableUntil = response.data.available_until;
                this.message = response.data.message;
                this.messageType = 'success';

                // Clear message after 3 seconds
                setTimeout(() => {
                    this.message = '';
                }, 3000);

            } catch (error) {
                this.isAvailable = !this.isAvailable; // Revert on error
                this.message = error.response?.data?.message || 'Failed to update availability';
                this.messageType = 'error';
            } finally {
                this.loading = false;
            }
        },

        async updateDuration() {
            if (!this.isAvailable) return;

            this.loading = true;
            try {
                const response = await axios.post('/api/v1/availability/toggle', {
                    available: true,
                    duration: this.duration
                });

                this.availableUntil = response.data.available_until;
                this.message = `Extended to ${this.duration} hours`;
                this.messageType = 'success';

                setTimeout(() => {
                    this.message = '';
                }, 3000);
            } catch (error) {
                this.message = 'Failed to update duration';
                this.messageType = 'error';
            } finally {
                this.loading = false;
            }
        },

        startTimer() {
            this.timer = setInterval(() => {
                if (this.isAvailable && this.availableUntil) {
                    const now = new Date();
                    const until = new Date(this.availableUntil);
                    
                    if (now >= until) {
                        this.isAvailable = false;
                        this.availableUntil = null;
                        this.message = 'Availability expired';
                        this.messageType = 'info';
                    }
                }
            }, 60000); // Check every minute
        }
    }
};
</script>

<style scoped>
.available-now-widget {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
}

.toggle-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.toggle-container.active {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.toggle-header {
    display: flex;
    align-items: center;
    gap: 12px;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #9ca3af;
    transition: all 0.3s ease;
}

.status-indicator.online {
    background: #fbbf24;
    box-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.status-text {
    font-weight: 600;
    font-size: 16px;
}

.toggle-switch {
    position: relative;
    width: 56px;
    height: 30px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 30px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 24px;
    width: 24px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

input:checked + .slider {
    background-color: #34d399;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

input:disabled + .slider {
    opacity: 0.5;
    cursor: not-allowed;
}

.availability-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e5e7eb;
}

.time-remaining {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #059669;
    font-weight: 500;
}

.duration-select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: white;
    font-size: 14px;
    cursor: pointer;
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

