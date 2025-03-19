<template>
    <div class="card">
        <div class="header">
            <h2>Participants</h2>
        </div>
        <div class="body">
            <div v-if="loading" class="flex justify-center items-center py-8">
                <div class="spinner"></div>
            </div>
            <div v-else-if="participants.length === 0" class="text-center py-8">
                <p class="text-gray-500">No participants yet.</p>
            </div>
            <ul v-else class="comment-reply list-unstyled">
                <li v-for="participant in participants" :key="participant.id" class="row clearfix">
                    <div class="icon-box col-md-2 col-4">
                        <img v-if="participant.user.avatar" class="img-fluid img-thumbnail"
                        :src="'/storage/' + participant.user.avatar" :alt="participant.user.name">
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="text-box col-md-10 col-8 p-l-0 p-r0">
                        <h5 class="m-b-0">{{ participant.user.name }}</h5>
                        <p>{{ participant.user.email }}</p>
                        <p :class="getStatusClasses(participant.status)">
                        Status: {{ formatStatus(participant.status) }}
                        </p>
                        <ul class="list-inline">
                            <li><span>{{ formatDate(participant.created_at) }}</span></li>
                            <li>
                                <template v-if="participant.status === 'voted' || participant.status === 'confirmed'" class="space-x-2">
                                    <button
                                        @click="markAttendance(participant.id, 'attended')"
                                        class="btn text-green-600 hover:text-green-900"
                                        :disabled="submitting"
                                    >
                                        Mark as Attended
                                    </button>
                                    <button
                                        @click="markAttendance(participant.id, 'no_show')"
                                        class="btn text-red-600 hover:text-red-900"
                                        :disabled="submitting"
                                    >
                                        Mark as No-Show
                                    </button>
                                </template>
                                <template v-else>
                                    <span class="text-gray-400">Already processed</span>
                                </template>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      initialParticipants: {
        type: Array,
        required: false,
        default: () => []
      },
      eventId: {
        type: [Number, String],
        required: true
      },
      isEventCreator: {
        type: Boolean,
        required: false,
        default: false
      },
      eventActive: {
        type: Boolean,
        required: false,
        default: true
      },
      apiUrl: {
        type: String,
        required: false,
        default: null
      }
    },
    data() {
      return {
        participants: this.initialParticipants,
        loading: false,
        submitting: false
      }
    },
    mounted() {
        console.log(this.initialParticipants);
      if (this.apiUrl && this.participants.length === 0) {
        this.loadParticipants();
      }
    },
    methods: {
      loadParticipants() {
        this.loading = true;

        fetch(this.apiUrl || `/api/events/${this.eventId}/participants`)
          .then(response => response.json())
          .then(data => {
            this.participants = data;
            this.loading = false;
          })
          .catch(error => {
            console.error('Error loading participants:', error);
            this.loading = false;
          });
      },
      formatStatus(status) {
        switch (status) {
          case 'voted': return 'Signed Up';
          case 'confirmed': return 'Confirmed';
          case 'attended': return 'Attended';
          case 'no_show': return 'No-Show';
          default: return status;
        }
      },
      getStatusClasses(status) {
        let baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full';

        switch (status) {
          case 'voted':
            return `${baseClasses} bg-yellow-100 text-yellow-800`;
          case 'confirmed':
            return `${baseClasses} bg-blue-100 text-blue-800`;
          case 'attended':
            return `${baseClasses} bg-green-100 text-green-800`;
          case 'no_show':
            return `${baseClasses} bg-red-100 text-red-800`;
          default:
            return `${baseClasses} bg-gray-100 text-gray-800`;
        }
      },
      formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleDateString(undefined, options);
      },
      markAttendance(participantId, status) {
        if (this.submitting) return;

        if (!confirm(`Are you sure you want to mark this participant as ${status === 'attended' ? 'attended' : 'absent'}?`)) {
          return;
        }

        this.submitting = true;

        const formData = new FormData();
        formData.append('status', status);
        formData.append('_method', 'PUT');

        fetch(`/events/${this.eventId}/participants/${participantId}`, {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Failed to update participant status');
          }
          return response.json();
        })
        .then(() => {
          // Update participant status in the list
          const participant = this.participants.find(p => p.id === participantId);
          if (participant) {
            participant.status = status;
          }

          this.submitting = false;
          this.$emit('status-updated', { participantId, status });
        })
        .catch(error => {
          console.error('Error updating participant status:', error);
          this.submitting = false;
          alert('An error occurred while updating the participant status. Please try again.');
        });
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
