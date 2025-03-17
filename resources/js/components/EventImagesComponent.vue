<template>
    <div class="event-images">
      <div v-if="canUpload" class="mb-6">
        <form @submit.prevent="uploadImage" class="space-y-4">
          <div>
            <label for="event_image" class="block text-sm font-medium text-gray-700">Upload Event Image</label>
            <div class="mt-1 flex items-center">
              <input
                type="file"
                id="event_image"
                ref="imageInput"
                @change="handleImageSelect"
                accept="image/*"
                class="block w-full text-sm text-gray-500
                       file:mr-4 file:py-2 file:px-4
                       file:rounded-md file:border-0
                       file:text-sm file:font-semibold
                       file:bg-blue-50 file:text-blue-700
                       hover:file:bg-blue-100"
                required
              >
              <button
                type="submit"
                class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                :disabled="uploading || !selectedImage"
              >
                <span v-if="uploading">Uploading...</span>
                <span v-else>Upload</span>
              </button>
            </div>
            <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
            <p class="mt-2 text-sm text-gray-500">
              You can upload up to {{ maxImages }} images. {{ remainingUploads }} uploads remaining.
            </p>
          </div>
          <div v-if="imagePreview" class="mt-2">
            <img :src="imagePreview" alt="Preview" class="h-32 w-auto rounded-md">
          </div>
        </form>
      </div>

      <div class="images-grid">
        <div v-if="loading" class="flex justify-center items-center py-8">
          <div class="spinner"></div>
        </div>
        <div v-else-if="images.length === 0" class="text-center py-8">
          <p class="text-gray-500">No images have been uploaded for this event.</p>
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div v-for="image in images" :key="image.id" class="relative group">
            <img
              :src="'/storage/' + image.image_path"
              alt="Event Image"
              class="w-full h-48 object-cover rounded-lg"
              @click="openImageModal(image)"
            />
            <div class="absolute inset-0 flex items-end p-2 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
              <div class="w-full flex justify-between items-center">
                <span class="text-xs text-white">
                  Uploaded by {{ image.user.name }}
                </span>
                <button
                  v-if="canDeleteImage(image)"
                  @click.stop="deleteImage(image.id)"
                  class="text-white hover:text-red-300"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Image Modal -->
      <div v-if="selectedImage" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75" @click="closeImageModal">
        <div class="max-w-4xl max-h-full overflow-auto" @click.stop>
          <img
            :src="'/storage/' + selectedImage.image_path"
            alt="Event Image"
            class="max-w-full max-h-[80vh] object-contain"
          />
          <div class="bg-gray-800 p-4 text-white">
            <p>Uploaded by {{ selectedImage.user.name }} on {{ formatDate(selectedImage.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      eventId: {
        type: [Number, String],
        required: true
      },
      initialImages: {
        type: Array,
        required: false,
        default: () => []
      },
      canUpload: {
        type: Boolean,
        required: false,
        default: false
      },
      maxImages: {
        type: Number,
        required: false,
        default: 10
      },
      currentUserId: {
        type: [Number, String],
        required: false,
        default: null
      },
      isEventCreator: {
        type: Boolean,
        required: false,
        default: false
      }
    },
    data() {
      return {
        images: this.initialImages,
        loading: false,
        uploading: false,
        selectedImage: null,
        imagePreview: null,
        selectedFile: null,
        error: null
      }
    },
    computed: {
      remainingUploads() {
        return Math.max(0, this.maxImages - this.images.length);
      }
    },
    mounted() {
      if (this.images.length === 0) {
        this.loadImages();
      }
    },
    methods: {
      loadImages() {
        this.loading = true;

        fetch(`/api/events/${this.eventId}/images`)
          .then(response => response.json())
          .then(data => {
            this.images = data;
            this.loading = false;
          })
          .catch(error => {
            console.error('Error loading images:', error);
            this.loading = false;
          });
      },
      handleImageSelect(event) {
        const file = event.target.files[0];
        if (!file) {
          this.selectedFile = null;
          this.imagePreview = null;
          return;
        }

        // Check if file is an image
        if (!file.type.startsWith('image/')) {
          this.error = 'Please select an image file';
          this.selectedFile = null;
          this.imagePreview = null;
          return;
        }

        // Check file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
          this.error = 'Image size should not exceed 2MB';
          this.selectedFile = null;
          this.imagePreview = null;
          return;
        }

        this.error = null;
        this.selectedFile = file;

        // Create image preview
        const reader = new FileReader();
        reader.onload = e => {
          this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      },
      uploadImage() {
        if (!this.selectedFile || this.uploading) return;

        if (this.images.length >= this.maxImages) {
          this.error = `You have reached the maximum of ${this.maxImages} images for this event`;
          return;
        }

        this.uploading = true;
        this.error = null;

        const formData = new FormData();
        formData.append('image', this.selectedFile);

        fetch(`/events/${this.eventId}/images`, {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => {
          if (!response.ok) {
            return response.json().then(data => {
              throw new Error(data.message || 'Failed to upload image');
            });
          }
          return response.json();
        })
        .then(data => {
          // Add the new image to the images array
          this.images.unshift(data.data);

          // Reset form
          this.selectedFile = null;
          this.imagePreview = null;
          this.$refs.imageInput.value = '';

          this.uploading = false;
        })
        .catch(error => {
          this.error = error.message || 'An error occurred while uploading the image';
          this.uploading = false;
        });
      },
      deleteImage(imageId) {
        if (!confirm('Are you sure you want to delete this image?')) {
          return;
        }

        fetch(`/api/events/${this.eventId}/images/${imageId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Failed to delete image');
          }

          // Remove the image from the array
          this.images = this.images.filter(image => image.id !== imageId);

          // Close the modal if the deleted image is currently selected
          if (this.selectedImage && this.selectedImage.id === imageId) {
            this.selectedImage = null;
          }
        })
        .catch(error => {
          console.error('Error deleting image:', error);
          alert('An error occurred while deleting the image. Please try again.');
        });
      },
      openImageModal(image) {
        this.selectedImage = image;
      },
      closeImageModal() {
        this.selectedImage = null;
      },
      canDeleteImage(image) {
        // User can delete the image if they are the event creator or the image uploader
        return this.isEventCreator || (this.currentUserId && image.user_id == this.currentUserId);
      },
      formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleDateString(undefined, options);
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
