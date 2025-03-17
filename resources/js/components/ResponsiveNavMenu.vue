<template>
    <div class="responsive-nav-menu">
      <button
        @click="isOpen = !isOpen"
        type="button"
        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
        aria-controls="mobile-menu"
        :aria-expanded="isOpen"
      >
        <span class="sr-only">Open main menu</span>
        <!-- Icon when menu is closed -->
        <svg
          :class="{'hidden': isOpen, 'block': !isOpen }"
          class="h-6 w-6"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          aria-hidden="true"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>

        <svg
          :class="{'block': isOpen, 'hidden': !isOpen }"
          class="h-6 w-6"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          aria-hidden="true"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div v-if="isOpen" id="mobile-menu" class="md:hidden">
          <div class="pt-2 pb-3 space-y-1">
            <a
              v-for="(item, index) in navItems"
              :key="index"
              :href="item.url"
              :class="[
                item.current
                  ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
                  : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800',
                'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
              ]"
            >
              {{ item.name }}
            </a>
          </div>

          <div v-if="user" class="pt-4 pb-3 border-t border-gray-200">
            <div class="flex items-center px-4">
              <div class="flex-shrink-0">
                <img
                  v-if="user.avatar"
                  :src="'/storage/' + user.avatar"
                  :alt="user.name"
                  class="h-10 w-10 rounded-full"
                >
                <div
                  v-else
                  class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center"
                >
                  <span class="text-gray-600 text-sm font-medium">
                    {{ user.name.charAt(0) }}
                  </span>
                </div>
              </div>
              <div class="ml-3">
                <div class="text-base font-medium text-gray-800">{{ user.name }}</div>
                <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
              </div>
            </div>
            <div class="mt-3 space-y-1">
              <a
                v-for="(item, index) in userMenuItems"
                :key="index"
                :href="item.url"
                class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
              >
                {{ item.name }}
              </a>
              <form method="POST" :action="logoutUrl">
                <input type="hidden" name="_token" :value="csrfToken">
                <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                  Sign out
                </button>
              </form>
            </div>
          </div>

          <div v-else class="pt-4 pb-3 border-t border-gray-200">
            <div class="space-y-1">
              <a
                href="/login"
                class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
              >
                Log in
              </a>
              <a
                href="/register"
                class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
              >
                Register
              </a>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </template>

  <script>
  export default {
    props: {
      user: {
        type: Object,
        required: false,
        default: null
      },
      navItems: {
        type: Array,
        required: true
      },
      userMenuItems: {
        type: Array,
        required: false,
        default: () => []
      },
      logoutUrl: {
        type: String,
        required: false,
        default: '/logout'
      },
      csrfToken: {
        type: String,
        required: true
      }
    },
    data() {
      return {
        isOpen: false
      }
    },
    watch: {
      '$route'() {
        this.isOpen = false;
      }
    },
    mounted() {
      document.addEventListener('click', this.clickOutside);
    },
    beforeDestroy() {
      document.removeEventListener('click', this.clickOutside);
    },
    methods: {
      clickOutside(event) {
        const el = this.$el;
        if (this.isOpen && el && !el.contains(event.target)) {
          this.isOpen = false;
        }
      }
    }
  }
  </script>

  <style scoped>
  .responsive-nav-menu {
    position: relative;
  }
  </style>