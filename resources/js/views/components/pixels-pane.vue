<template>
  <div id="pixels-pane">
    <input
      type="search"
      v-model="search"
      v-on:keyup.enter="handleSearch"
      placeholder="Search from pixels"
      class="form-control mt-1"
    />
    <div class="video-overlay" v-if="videos.length === 0 && searching">
      <img src="/images/icons/spinner.svg" />
    </div>
    <div class="video-lists" v-if="videos.length > 0">
      <masonry :cols="{ default: 3, 700: 1 }" :gutter="15">
        <div
          class="video-card"
          v-for="(pexel, idx) in videos"
          :key="`video-${idx}`"
        >
          <img :src="getUrl(pexel)" @click="handleSelect(pexel)" :id="`pexel-video-${idx}`" />
          <b-popover
            :target="`pexel-video-${idx}`"
            triggers="hover"
            placement="top"
          >
            <span style="font-size: 1rem; font-weight:700;">
              This video was provided by
              <a
                style="text-decoration: underline;color: #1998e4;cursor: pointer;pointer-event: auto;"
                :href="pexel.user.url"
                target="_blank"
                >{{ pexel.user.name }}</a
              >
              on Pexel.
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
      Not found!
    </h5>
  </div>
</template>

<style lang="scss" scoped>
#pixels-pane {
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
  name: 'pixels-pane',
  data() {
    return {
      search: '',
      searching: false,
      videos: [],
      totalCount: 0,
      pageLimit: 8,
      page: 1
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
        .get(`/api/pixels-video?text=${this.search}&page=${this.page}`)
        .then(res => {
          if (res && res.status === 200) {
            this.videos = [...this.videos, ...res.data.videos.videos];
            this.page += 1;
            this.totalCount = res.data.videos.total_results;
            if (this.videos.length >= this.totalCount) {
              this.page = this.pageLimit + 2;
            }
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot load videos from pixels',
              'danger'
            );
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot load videos from pixels',
            'danger'
          );
        })
        .finally(() => {
          this.searching = false;
        });
    },
    getUrl(p) {
      return p.image;
    },
    handleSelect(p) {
      let video = '';
      if (p.video_files.length > 1) {
        video = p.video_files[p.video_files.length - 2].link;
      } else {
        video = p.video_files[0].link;
      }
      this.$emit('onSelect', {
        video,
        type: 'pexels',
        thumb: p.image
      });
    }
  }
};
</script>
