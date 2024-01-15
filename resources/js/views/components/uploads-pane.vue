<template>
  <div id="uploads-pane">
    <div class="video-overlay" v-if="videos.length === 0 && searching">
      <img src="/images/icons/spinner.svg" />
    </div>
    <div class="video-lists" v-if="videos.length > 0">
      <div
        class="video-card"
        v-for="(video, idx) in videos"
        :key="`uploads-${idx}`"
      >
        <img :src="getUrl(video)" @click="handleSelect(video)" @error="defaultImage" />
      </div>
    </div>
    <h5 class="text-center mt-3" v-if="videos.length === 0 && !searching">
      Not found!
    </h5>
  </div>
</template>

<style lang="scss" scoped>
#uploads-pane {
  .video-lists {
    margin-top: 10px;
    display: flex;
    flex-wrap: wrap;
    max-height: calc(100vh - 247px);
    overflow-y: auto;
    .video-card {
      width: 150px;
      height: 100px;
      margin-bottom: 15px;
      margin-right: 15px;
      border-radius: 10px;
      cursor: pointer;
      overflow: hidden;
      display: flex;
      justify-content: center;
      background-color: black;
      img {
        max-width: 100%;
        border-radius: 10px;
      }
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
  data() {
    return {
      videos: [],
      search: '',
      searching: false,
      awsUrl: 'https://vidpopup.s3.amazonaws.com/'
    };
  },
  mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;
    this.searchVideos();
  },
  methods: {
    handleSearch() {},
    searchVideos() {
      this.searching = true;
      axios
        .get(`/api/uploaded-video`, { text: this.search })
        .then(res => {
          this.videos = res.data.videos;
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
    getUrl(video) {
      return `${this.awsUrl}${video.thumbnail}`;
    },
    handleSelect(video) {
      this.$emit('onSelect', {
        video: `${video.path}`,
        type: 'uploads',
        thumb: `${video.thumbnail}`
      });
    },
    defaultImage(e) {
      e.target.src = '/images/placeholder.png';
    }
  }
};
</script>
