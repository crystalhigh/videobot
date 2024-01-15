<template>
  <div id="giphy-pane">
    <input
      type="search"
      v-model="search"
      v-on:keyup.enter="handleSearch"
      placeholder="Search from giphy"
      class="form-control mt-1"
    />
    <div class="video-overlay" v-if="videos.length === 0 && searching">
      <img src="/images/icons/spinner.svg" />
    </div>
    <div class="video-lists" v-if="videos.length > 0">
      <masonry :cols="{default: 3, 700: 1}" :gutter="15">
        <div
          class="video-card"
          v-for="(giphy, idx) in videos"
          :key="`video-${idx}`"
        >
          <img :src="getUrl(giphy)" @click="handleSelect(giphy)" :id="`giphy-video-${idx}`" />
          <b-popover
            :target="`giphy-video-${idx}`"
            triggers="hover"
            placement="top"
          >
            <span style="font-size: 1rem; font-weight:700;">
              This video was provided by
              <a
                style="text-decoration: underline;color: #1998e4;cursor: pointer;pointer-event: auto;"
                :href="giphy.url"
                target="_blank"
                >{{ giphy.user.username }}</a
              >
              on Giphy.
            </span>
          </b-popover>
        </div>
      </masonry>
      <div class="text-center mt-3 w-100" v-if="page < pageLimit">
        <button
          type="button"
          class="btn btn-loadmore"
          @click="searchVideos()"
          :disabled="searching || page > pageLimit"
        >
          <fa-icon :icon="['fas', 'spinner']" spin v-if="searching" />
          Load More
        </button>
      </div>
    </div>
    <h5 class="text-center mt-3" v-if="videos.length === 0 && !searching">
      Not Found!
    </h5>
  </div>
</template>

<style lang="scss" scoped>
#giphy-pane {
  .video-lists {
    margin-top: 10px;
    max-height: calc(100vh - 247px);
    overflow-y: auto;
    .video-card {
      cursor: pointer;
      overflow: hidden;
      margin-bottom: 10px;
      img {
        max-width: 100%;
        border-radius: 7px;
        background-color: black;
      }
    }
    .btn-loadmore {
      padding: 7px 30px;
      background-color: black;
      color: white;
    }
  }
  .video-overlay {
    display: flex;
    align-items: center;
    justify-content: center;
    height: calc(100vh - 247px);
  }
}
</style>

<script>
import axios from 'axios';
export default {
  name: 'pixabay-pane',
  data() {
    return {
      search: '',
      searching: false,
      videos: [],
      page: 1,
      totalCount: 0,
      pageLimit: 8
    };
  },
  mounted() {
    this.searchVideos();
  },
  methods: {
    handleSearch() {
      this.videos = [];
      this.page = 1;
      this.searchVideos();
    },
    searchVideos() {
      if (this.page > this.pageLimit) return;
      this.searching = true;
      axios
        .get(`/api/giphy-video?text=${this.search}&page=${this.page}`)
        .then(res => {
          if (res && res.data.videos) {
            this.videos = [...this.videos, ...res.data.videos.data];
            this.totalCount = res.data.videos.pagination.total_count;
            this.page += 1;
            if (this.videos.length >= this.totalCount) {
              this.page = this.pageLimit + 2;
            }
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot load videos from pixabay',
            'danger'
          );
        })
        .finally(() => {
          this.searching = false;
        });
    },
    getUrl(p) {
      if (p.images.downsized_medium) {
        return p.images.downsized_medium.url;
      } else if (p.images.fixed_width) {
        return p.images.fixed_width.url;
      }
      return '';
    },
    handleSelect(p) {
      this.$emit('onSelect', {
        video: `https://i.giphy.com/media/${p.id}/giphy.mp4`,
        type: 'giphy',
        thumb: this.getUrl(p)
      });
    }
  }
};
</script>
