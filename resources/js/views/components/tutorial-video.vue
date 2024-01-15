<template>
  <b-modal
    id="tutorial-video-modal"
    hide-header
    hide-footer
    centered
    no-close-on-backdrop
    ref="tutorialVideo"
    size="xl"
  >
    <div class="tutorial-modal-body">
      <div class="video-container">
        <iframe
          src="https://www.youtube.com/embed/TgYGy4oUiJM"
          frameborder="0"
          allowfullscreen
          class="video"
        ></iframe>
      </div>
      <div class="d-flex align-items-center justify-content-between px-4 py-3">
        <b-form-checkbox v-model="show">
          <span class="cursor-pointer"
            >Don't show this again</span
          ></b-form-checkbox
        >
        <b-button variant="secondary" @click="handleClose">Close</b-button>
      </div>
    </div>
  </b-modal>
</template>

<style lang="scss" moduled>
#tutorial-video-modal {
  .modal-body {
    padding: 0px;
  }
  .video-container {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 56.25%;
  }
  .video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
  .cursor-pointer {
    cursor: pointer;
  }
}
</style>

<script>
import { TUTORIAL } from '@/services/store/auth.module';
import axios from 'axios';
export default {
  data() {
    return {
      show: false
    };
  },
  methods: {
    handleClose() {
      this.$store.dispatch(TUTORIAL, 0);
      if (this.show) {
        axios.get('/api/update-show-tutorial');
      }
      this.$bvModal.hide('tutorial-video-modal');
    }
  }
};
</script>
