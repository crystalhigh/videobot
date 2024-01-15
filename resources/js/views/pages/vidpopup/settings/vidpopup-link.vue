<template>
  <div id="vidpopup-flow-link">
    <div class="flow-headers">
      <router-link :to="`/app/vidgen/${id}/settings/settings`" tag="span">
        SEO
      </router-link>
      <router-link :to="`/app/vidgen/${id}/settings/advance`" tag="span">
        Advance
      </router-link>
      <span class="active">Link</span>
      <router-link :to="`/app/vidgen/${id}/settings/widget`" tag="span">
        Widget
      </router-link>
    </div>
    <div class="content-wrapper">
      <div
        class="d-flex align-items-center justify-content-between mb-1 mt-4 px-1"
      >
        <h5 class="title mb-0">Share your URL :</h5>
        <button
          class="btn btn-sm btn-outline-primary mt-2"
          @click="handleCopyShareURL"
        >
          {{ shareButton }}
        </button>
      </div>
      <b-form-input
        id="share-url"
        v-model="shareUrl"
        type="text"
        class="form-control border-rounded"
      ></b-form-input>

      <div
        class="d-flex align-items-center justify-content-between mb-1 mt-4 px-1"
      >
        <h5 class="title mb-0">Preview mode :</h5>
        <button
          class="btn btn-sm btn-outline-primary mt-2"
          @click="handleCopyPreviewURL"
        >
          {{ previewButton }}
        </button>
      </div>

      <b-form-input
        id="preview-mode"
        v-model="previewUrl"
        type="text"
        class="form-control border-rounded"
      ></b-form-input>

      <div class="space-between mt-4">
        <h6 class="mb-0">Share on social media :</h6>
        <div class="d-flex align-items-center social-icons">
          <a
            :href="`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`"
            target="_blank"
          >
            <img src="/images/icons/facebook.svg" />
          </a>
          <a
            :href="
              `https://www.linkedin.com/sharing/share-offsite/?url=${shareUrl}`
            "
            target="_blank"
          >
            <img class="ml-1" src="/images/icons/linkedin.svg" />
          </a>
          <a
            :href="`https://twitter.com/intent/tweet?text=${shareUrl}`"
            target="_blank"
          >
            <img class="ml-1" src="/images/icons/twitter.svg" />
          </a>
        </div>
      </div>
    </div>
    <div class="vidpopup-step-button-group">
      <div>
        <button type="button" @click="handleBack" class="btn circle-button">
          <fa-icon :icon="['fas', 'arrow-left']" />
        </button>
        <button type="button" @click="handleNext" class="btn circle-button">
          <fa-icon :icon="['fas', 'arrow-right']" />
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
#vidpopup-flow-link {
  height: 100%;
  display: flex;
  flex-direction: column;
  .ps {
    height: 550px;
  }
  .content-wrapper {
    overflow-y: auto;
    padding: 0px 10px 50px 0px;
    .add-more-step {
      border-radius: 30px;
    }
    .border-rounded {
      border-radius: 5px;
      border: 2px dashed #1998e4;
      color: #1998e4;
      font-weight: 600;
      font-size: 13px;
    }
  }
  .social-icons {
    img {
      cursor: pointer;
    }
  }
}
</style>

<script>
import { mapGetters } from 'vuex';
export default {
  name: 'vidpopup-link',
  computed: {
    ...mapGetters(['vidpop', 'currentUser'])
  },
  data() {
    return {
      previewUrl: '',
      shareUrl: '',
      id: '',
      shareButton: 'Copy',
      previewButton: 'Copy'
    };
  },
  beforeMount() {
    if (!this.currentUser || (this.currentUser && this.currentUser.role != 'agent')) {
      this.$func.makeToast(
        this,
        'Error',
        'You are not allowed to visit this page!',
        'danger'
      );
      if (this.currentUser.role == 'admin')
        this.$router.push({path: '/app/admin-payout'});
      else
        this.$router.push({path: '/app/statistics'});
    }
  },
  mounted() {
    this.id = this.$route.params.id;

    this.originUrl = window.location.origin;
    this.shareUrl = `${window.location.origin}/live/${this.vidpop.code}`;
    this.previewUrl = `${window.location.origin}/live/${this.vidpop.code}?preview=1`;
  },
  methods: {
    handleBack() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/settings/advance`
      });
    },
    handleNext() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/settings/email`
      });
    },
    handleCopyShareURL() {
      let testingCodeToCopy = document.querySelector('#share-url');
      testingCodeToCopy.setAttribute('type', 'text');
      testingCodeToCopy.select();
      try {
        var successful = document.execCommand('copy');
        if (successful) {
          this.shareButton = 'Copied';
        }
      } catch (err) {}
    },
    handleCopyPreviewURL() {
      let testingCodeToCopy = document.querySelector('#preview-mode');
      testingCodeToCopy.setAttribute('type', 'text');
      testingCodeToCopy.select();
      try {
        var successful = document.execCommand('copy');
        if (successful) {
          this.shareButton = 'Copied';
        }
      } catch (err) {}
      this.previewButton = 'Copied';
    }
  }
};
</script>
