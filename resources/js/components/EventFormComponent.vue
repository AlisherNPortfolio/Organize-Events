<template>
    <div class="event-form">
      <h3 class="event-form-heading">{{ isEdit ? 'Edit Event' : 'Create New Event' }}</h3>

      <div v-if="errors.length > 0" class="alert alert-danger mb-4">
        <strong>Please fix the following errors:</strong>
        <ul class="mt-2 mb-0">
          <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
        </ul>
      </div>

      <form @submit.prevent="submitForm" class="needs-validation">
        <div class="event-form-section">
          <h4 class="event-form-section-title">Basic Information</h4>

          <div class="mb-3">
            <label for="event_type" class="form-label">Event Type</label>
            <select
              id="event_type"
              v-model="form.event_type"
              class="form-select"
            >
              <option value="custom">Custom Event</option>
              <option value="sport">Sport Event</option>
              <option value="meetup">Meetup Event</option>
              <option value="travel">Travel Event</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="name" class="form-label">Event Name</label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              class="form-control"
              required
            >
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="form-control"
              required
            ></textarea>
          </div>
        </div>

        <div class="event-form-section">
          <h4 class="event-form-section-title">Date and Location</h4>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="mb-3 date-picker">
                <label for="event_date" class="form-label">Event Date</label>
                <input
                  type="date"
                  id="event_date"
                  v-model="form.event_date"
                  class="form-control"
                  :min="minDate"
                  required
                >
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3 time-picker">
                <label for="start_time" class="form-label">Start Time</label>
                <input
                  type="time"
                  id="start_time"
                  v-model="form.start_time"
                  class="form-control"
                  required
                >
              </div>
            </div>
          </div>

          <div class="mb-3 time-picker">
            <label for="end_time" class="form-label">End Time (optional)</label>
            <input
              type="time"
              id="end_time"
              v-model="form.end_time"
              class="form-control"
            >
          </div>

          <div class="mb-3">
            <label for="address" class="form-label">Meeting Address</label>
            <input
              type="text"
              id="address"
              v-model="form.address"
              class="form-control"
              required
            >
          </div>
        </div>

        <div class="event-form-section">
          <h4 class="event-form-section-title">Participation Settings</h4>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="min_participants" class="form-label">Minimum Participants</label>
                <input
                  type="number"
                  id="min_participants"
                  v-model.number="form.min_participants"
                  min="1"
                  class="form-control"
                  required
                >
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="max_participants" class="form-label">Maximum Participants</label>
                <input
                  type="number"
                  id="max_participants"
                  v-model.number="form.max_participants"
                  :min="form.min_participants || 1"
                  class="form-control"
                  required
                >
              </div>
            </div>
          </div>

          <div class="mb-4">
            <label for="voting_expiry_time" class="form-label">Voting Expiry Date and Time</label>
            <input
              type="datetime-local"
              id="voting_expiry_time"
              v-model="form.voting_expiry_time"
              class="form-control"
              :min="minDateTime"
              :max="maxVotingDateTime"
              required
            >
            <div class="form-text">Participants can sign up until this date/time</div>
          </div>
        </div>

        <div class="event-form-section">
          <h4 class="event-form-section-title">Event Image</h4>

          <div class="mb-3">
            <label for="image" class="form-label">Event Image (optional)</label>
            <input
              type="file"
              class="form-control"
              id="image"
              @change="handleImageUpload"
              accept="image/*"
            >
            <div class="form-text">Upload an image to make your event more attractive (max 2MB)</div>
          </div>

          <div v-if="imagePreview" class="mt-3">
            <img :src="imagePreview" alt="Preview" class="image-preview" style="max-height: 200px;">
          </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
          <button
            type="button"
            @click="$emit('cancel')"
            class="btn btn-outline-secondary me-2"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="btn btn-primary btn-submit"
            :disabled="submitting"
          >
            <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
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
