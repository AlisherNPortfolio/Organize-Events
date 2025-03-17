<template>
<div class="events-container">
    <div v-if="loading" class="flex justify-center items-center py-8">
        <div class="spinner">
        </div>
    </div>
    <div v-else-if="events.length === 0" class="text-center py-8">
        <p class="text-gray-500">No events found.</p>
    </div>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="event in events" :key="event.id" class="bg-white rounded-lg shadow-md overflow-hidden">
            <div v-if="event.image_path" class="h-48 overflow-hidden">
                <img :src="'/storage/' + event.image_path" :alt="event.name" class="w-full h-full object-cover">
            </div>
            <div v-else class="h-48 bg-gray-200 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="p-4">
                <h3 class="text-xl font-semibold mb-2">{{ event.name }}</h3>
                <p class="text-gray-600 mb-4 truncate">{{ event.description }}</p>
                <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ formatDate(event.event_date) }}
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ truncateText(event.address, 20) }}
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="text-sm">
                        <span class="font-medium">{{ event.participants_count || 0 }}</span> / {{ event.max_participants }} participants
                    </div>
                    <a :href="'/events/' + event.id" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">View Details</a>
                </div>
            </div>
        </div>
    </div>
</div>
</template>


<script>
export default {
    props: {
        initialEvents: {
            type: Array,
            required: false,
            default: () => []
        },
        apiUrl: {
            type: String,
            required: false,
            default: '/api/events'
        },
        autoload: {
            type: Boolean,
            required: false,
            default: true
        }
    },
    data() {
        return {
            events: this.initialEvents,
            loading: false
        }
    },
    mounted() {
        if (this.autoload && this.events.length === 0) {
            this.loadEvents();
        }
    },
    methods: {
        loadEvents() {
            this.loading = true;

            fetch(this.apiUrl)
                .then(response => response.json())
                .then(data => {
                    this.events = data;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Error loading events:', error);
                    this.loading = false;
                });
        },
        formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString(undefined, options);
        },
        truncateText(text, length) {
            if (text.length <= length) return text;
            return text.substring(0, length) + '...';
        }
    }
}
</script>


<style scoped>
.spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border-left-color: #4f46e5;
    animation: spin 1s ease infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
