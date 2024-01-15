<template>
  <div id="pixabay-pane">
    <input
      type="search"
      v-model="search"
      v-on:keyup.enter="handleSearch"
      placeholder="Search from pixabay"
      class="form-control mt-1"
    />
    <div class="video-overlay" v-if="videos.length === 0 && searching">
      <img src="/images/icons/spinner.svg" />
    </div>
    <div class="video-lists" v-if="videos.length > 0">
      <masonry :cols="{ default: 3, 700: 1 }" :gutter="15">
        <div
          class="video-card"
          v-for="(pixabay, idx) in videos"
          :key="`video-${idx}`"
        >
          <img
            :src="getUrl(pixabay)"
            @click="handleSelect(pixabay)"
            :id="`pixabay-video-${idx}`"
          />
          <b-popover
            :target="`pixabay-video-${idx}`"
            triggers="hover"
            placement="top"
          >
            <span style="font-size: 1rem; font-weight:700;">
              This video was provided by
              <a
                style="text-decoration: underline;color: #1998e4;cursor: pointer;pointer-event: auto;"
                :href="pixabay.pageURL"
                target="_blank"
                >{{ pixabay.user }}</a
              >
              on Pixabay.
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
#pixabay-pane {
  .video-lists {
    margin-top: 10px;
    max-height: calc(100vh - 247px);
    overflow-y: auto;
    .video-card {
      cursor: pointer;
      overflow: hidden;
      margin-bottom: 10px;
      img {
        width: 100%;
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
      pageLimit: 4
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
        .get(`/api/pixabay-video?text=${this.search}&page=${this.page}`)
        .then(res => {
          if (res && res.status === 200) {
            this.videos = [...this.videos, ...res.data.videos.hits];
            this.totalCount = res.data.videos.totalHits;
            this.page += 1;
            if (this.videos.length >= this.totalCount) {
              this.page = this.pageLimit + 2;
            }
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot load videos from pixabay',
              'danger'
            );
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
      return `https://i.vimeocdn.com/video/${p.picture_id}_200x150.jpg`;
    },
    handleSelect(p) {
      let video = '';
      if (p.videos.medium) {
        video = p.videos.medium.url;
      } else if (p.videos.large) {
        video = p.videos.large.url;
      }
      this.$emit('onSelect', {
        video,
        type: 'pixabay',
        thumb: this.getUrl(p)
      });
    }
  }
};
</script>
