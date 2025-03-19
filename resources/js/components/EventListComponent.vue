<template>
<div class="events-container">
    <div v-if="loading" class="d-flex justify-content-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div v-else-if="events.length === 0" class="text-center py-5">
        <p class="text-muted">No events found.</p>
    </div>
    <div v-else>
        <!-- <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <div v-for="event in paginatedEvents" :key="event.id" class="col">
                <div class="card h-100 event-card">
                    <div v-if="event.image_path" class="card-img-top-container">
                        <img :src="'/storage/' + event.image_path" :alt="event.name" class="card-img-top event-image">
                    </div>
                    <div v-else class="bg-light d-flex align-items-center justify-content-center event-image">
                        <i class="bi bi-image text-secondary" style="font-size: 2rem;"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ event.name }}</h5>
                        <p class="card-text text-truncate">{{ event.description }}</p>
                        <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
                            <div>
                                <i class="bi bi-calendar me-1"></i> {{ formatDate(event.event_date) }}
                            </div>
                            <div>
                                <i class="bi bi-geo-alt me-1"></i> {{ truncateText(event.address, 20) }}
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="small">
                                <span class="fw-medium">{{ event.participants_count || 0 }}</span> / {{ event.max_participants }} participants
                            </div>
                            <a :href="'/events/' + event.id" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="row">
            <div v-for="event in paginatedEvents" :key="event.id" class="col-sm-12 col-md-6 col-lg-4 mb-4">

                <div class="card text-dark card-has-bg click-col" :style="event.image_path ? 'background-image:url('+ '/storage/' + event.image_path +');' : ''">
                    <img v-if="event.image_path" class="card-img d-none" :src="'/storage/' + event.image_path" :alt="event.name">
                    <img v-else src="/storage/default-event.png" alt="Default event" />
                    <div class="card-img-overlay d-flex flex-column">
                        <div class="card-body">
                            <small class="card-meta mb-2">{{ event.name }}</small>
                            <h4 class="card-title mt-0 "><a class="text-dark" :href="'/events/' + event.id">{{ event.name }}</a></h4>
                            <small><i class="far fa-clock"></i> {{ formatDate(event.event_date) }} {{ formatDate(event.end_time) }}</small>
                            <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
                                <div>
                                    <i class="bi bi-geo-alt me-1"></i> {{ truncateText(event.address, 20) }}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="media">
                                <!-- <img class="mr-3 rounded-circle" :src="'storage/' + event.user.avatar" alt="User Avatar" style="max-width:50px">
                                <div class="media-body">
                                    <h6 class="my-0 text-dark d-block">{{ event.user.name }}</h6>
                                    <small>Director of UI/UX</small>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-5" v-if="totalPages > 1">
            <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li v-for="page in displayedPages" :key="page" class="page-item" :class="{ active: currentPage === page, disabled: page === '...' }">
                    <a class="page-link" href="#" @click.prevent="page === '...' ? null : changePage(page)">{{ page }}</a>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
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
        },
        perPage: {
            type: Number,
            required: false,
            default: 9
        }
    },
    data() {
        return {
            events: this.initialEvents,
            loading: false,
            currentPage: 1
        }
    },
    computed: {
        totalPages() {
            return Math.ceil(this.events.length / this.perPage);
        },
        paginatedEvents() {
            const startIndex = (this.currentPage - 1) * this.perPage;
            const endIndex = startIndex + this.perPage;
            return this.events.slice(startIndex, endIndex);
        },
        displayedPages() {
            const pages = [];
            const totalPages = this.totalPages;
            const currentPage = this.currentPage;

            if (totalPages <= 7) {
                // If there are 7 or fewer pages, show all
                for (let i = 1; i <= totalPages; i++) {
                    pages.push(i);
                }
            } else {
                // Always show the first page
                pages.push(1);

                // Calculate start and end of the displayed page range
                let startPage = Math.max(2, currentPage - 1);
                let endPage = Math.min(totalPages - 1, currentPage + 1);

                // Adjust start and end to always show 3 pages
                if (startPage === 2) endPage = 4;
                if (endPage === totalPages - 1) startPage = totalPages - 3;

                // Add ellipsis if needed before the range
                if (startPage > 2) pages.push('...');

                // Add the page range
                for (let i = startPage; i <= endPage; i++) {
                    pages.push(i);
                }

                // Add ellipsis if needed after the range
                if (endPage < totalPages - 1) pages.push('...');

                // Always show the last page
                pages.push(totalPages);
            }

            return pages;
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
            if (!text) return '';
            if (text.length <= length) return text;
            return text.substring(0, length) + '...';
        },
        changePage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
            // Scroll to top of events section
            window.scrollTo({
                top: this.$el.offsetTop - 100,
                behavior: 'smooth'
            });
        }
    }
}
</script>
