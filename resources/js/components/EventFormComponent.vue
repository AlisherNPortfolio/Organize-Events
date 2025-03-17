<template>
    <div class="event-form">
      <form @submit.prevent="submitForm" class="space-y-6">
        <div v-if="errors.length > 0" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative mb-4">
          <strong class="font-bold">Please fix the following errors:
            </strong>
          <ul class="mt-2 list-disc list-inside">
            <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
          </ul>
        </div>

        <div class="space-y-4">
          <div>
            <label for="event_type" class="block text-sm font-medium text-gray-700">Event Type</label>
            <select
              id="event_type"
              v-model="form.event_type"
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option value="custom">Custom Event</option>
              <option value="sport">Sport Event</option>
              <option value="meetup">Meetup Event</option>
              <option value="travel">Travel Event</option>
            </select>
          </div>

          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Event Name</label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              required
            >
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              required
            ></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="event_date" class="block text-sm font-medium text-gray-700">Event Date</label>
              <input
                type="date"
                id="event_date"
                v-model="form.event_date"
                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                :min="minDate"
                required
              >
            </div>

            <div>
              <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
              <input
                type="time"
                id="start_time"
                v-model="form.start_time"
                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                required
              >
            </div>
          </div>

          <div>
            <label for="end_time" class="block text-sm font-medium text-gray-700">End Time (optional)</label>
            <input
              type="time"
              id="end_time"
              v-model="form.end_time"
              class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            >
          </div>

          <div>
            <label for="address" class="block text-sm font-medium text-gray-700">Meeting Address</label>
            <input
              type="text"
              id="address"
              v-model="form.address"
              class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              required
            >
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="min_participants" class="block text-sm font-medium text-gray-700">Minimum Participants</label>
              <input
                type="number"
                id="min_participants"
                v-model.number="form.min_participants"
                min="1"
                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                required
              >
            </div>

            <div>
              <label for="max_participants" class="block text-sm font-medium text-gray-700">Maximum Participants</label>
              <input
                type="number"
                id="max_participants"
                v-model.number="form.max_participants"
                :min="form.min_participants || 1"
                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                required
              >
            </div>
          </div>

          <div>
            <label for="voting_expiry_time" class="block text-sm font-medium text-gray-700">Voting Expiry Date and Time</label>
            <input
              type="datetime-local"
              id="voting_expiry_time"
              v-model="form.voting_expiry_time"
              class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              :min="minDateTime"
              :max="maxVotingDateTime"
              required
            >
          </div>

          <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Event Image (optional)</label>
            <input
              type="file"
              id="image"
              @change="handleImageUpload"
              accept="image/*"
              class="mt-1 block w-full text-sm text-gray-500
                     file:mr-4 file:py-2 file:px-4
                     file:rounded-md file:border-0
                     file:text-sm file:font-semibold
                     file:bg-blue-50 file:text-blue-700
                     hover:file:bg-blue-100"
            >
            <div v-if="imagePreview" class="mt-2">
              <img :src="imagePreview" alt="Preview" class="h-32 w-auto rounded-md">
            </div>
          </div>
        </div>

        <div class="flex justify-end">
          <button
            type="button"
            @click="$emit('cancel')"
            class="mr-2 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            :disabled="submitting"
          >
            {{ submitting ? 'Saving...' : (isEdit ? 'Update Event' : 'Create Event') }}
          </button>
        </div>
      </form>
    </div>
  </template>

  <script>
  export default {
    props: {
      initialData: {
        type: Object,
        required: false,
        default: () => ({})
      },
      isEdit: {
        type: Boolean,
        required: false,
        default: false
      },
      submitUrl: {
        type: String,
        required: true
      },
      submitMethod: {
        type: String,
        required: false,
        default: 'POST'
      }
    },
    data() {
      return {
        form: {
          name: this.initialData.name || '',
          description: this.initialData.description || '',
          min_participants: this.initialData.min_participants || 1,
          max_participants: this.initialData.max_participants || 10,
          event_date: this.initialData.event_date || this.getTomorrowDate(),
          start_time: this.initialData.start_time || '10:00',
          end_time: this.initialData.end_time || '',
          address: this.initialData.address || '',
          voting_expiry_time: this.initialData.voting_expiry_time || this.getTomorrowDateTime(),
          event_type: this.initialData.event_type || 'custom',
          image: null
        },
        imagePreview: this.initialData.image_path ? `/storage/${this.initialData.image_path}` : null,
        errors: [],
        submitting: false
      }
    },
    computed: {
      minDate() {
        const today = new Date();
        return today.toISOString().split('T')[0];
      },
      minDateTime() {
        const now = new Date();
        now.setMinutes(now.getMinutes() + 10); // at least 10 minutes from now
        return now.toISOString().slice(0, 16);
      },
      maxVotingDateTime() {
        if (!this.form.event_date) return null;

        const eventDate = new Date(this.form.event_date);
        eventDate.setHours(0, 0, 0, 0);
        return eventDate.toISOString().slice(0, 16);
      }
    },
    methods: {
      getTomorrowDate() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        return tomorrow.toISOString().split('T')[0];
      },
      getTomorrowDateTime() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(10, 0, 0, 0);
        return tomorrow.toISOString().slice(0, 16);
      },
      handleImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
          this.errors.push('Please select an image file');
          return;
        }

        if (file.size > 2 * 1024 * 1024) {
          this.errors.push('Image size should not exceed 2MB');
          return;
        }

        this.form.image = file;

        const reader = new FileReader();
        reader.onload = e => {
          this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      },
      validateForm() {
        this.errors = [];

        if (!this.form.name) {
          this.errors.push('Event name is required');
        }

        if (!this.form.description) {
          this.errors.push('Description is required');
        }

        if (!this.form.event_date) {
          this.errors.push('Event date is required');
        } else {
          const eventDate = new Date(this.form.event_date);
          const today = new Date();
          today.setHours(0, 0, 0, 0);

          if (eventDate < today) {
            this.errors.push('Event date must be in the future');
          }
        }

        if (!this.form.start_time) {
          this.errors.push('Start time is required');
        }

        if (this.form.end_time && this.form.start_time && this.form.end_time <= this.form.start_time) {
          this.errors.push('End time must be after start time');
        }

        if (!this.form.address) {
          this.errors.push('Meeting address is required');
        }

        if (this.form.min_participants < 1) {
          this.errors.push('Minimum participants must be at least 1');
        }

        if (this.form.max_participants < this.form.min_participants) {
          this.errors.push('Maximum participants must be greater than or equal to minimum participants');
        }

        if (!this.form.voting_expiry_time) {
          this.errors.push('Voting expiry time is required');
        } else {
          const votingExpiry = new Date(this.form.voting_expiry_time);
          const now = new Date();

          if (votingExpiry <= now) {
            this.errors.push('Voting expiry time must be in the future');
          }

          if (this.form.event_date) {
            const eventDate = new Date(this.form.event_date);
            eventDate.setHours(0, 0, 0, 0);

            if (votingExpiry >= eventDate) {
              this.errors.push('Voting expiry time must be before the event date');
            }
          }
        }

        return this.errors.length === 0;
      },
      submitForm() {
        if (!this.validateForm()) {
          return;
        }

        this.submitting = true;

        const formData = new FormData();

        Object.keys(this.form).forEach(key => {
          if (this.form[key] !== null && this.form[key] !== undefined && this.form[key] !== '') {
            formData.append(key, this.form[key]);
          }
        });

        if (this.submitMethod === 'PUT') {
          formData.append('_method', 'PUT');
        }

        fetch(this.submitUrl, {
          method: this.submitMethod === 'PUT' ? 'POST' : this.submitMethod,
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => {
          if (!response.ok) {
            return response.json().then(data => {
              throw new Error(JSON.stringify(data.errors || 'Error creating event'));
            });
          }
          return response.json();
        })
        .then(data => {
          this.submitting = false;
          this.$emit('success', data);
        })
        .catch(error => {
          this.submitting = false;

          try {
            const errorData = JSON.parse(error.message);

            if (typeof errorData === 'object') {
              Object.values(errorData).forEach(messages => {
                if (Array.isArray(messages)) {
                  messages.forEach(message => this.errors.push(message));
                } else {
                  this.errors.push(messages);
                }
              });
            } else {
              this.errors.push(errorData);
            }
          } catch (e) {
            this.errors.push(error.message || 'An error occurred while saving the event');
          }
        });
      }
    }
  }
  </script>
