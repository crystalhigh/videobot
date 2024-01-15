<template>
  <div id="step-flow-video">
    <div class="flow-headers">
      <router-link
        :to="`/app/vidgen/${id}/${step_id}/edit/overlay`"
        tag="span"
      >
        Text
      </router-link>
      <router-link
        :to="`/app/vidgen/${id}/${step_id}/edit/answer`"
        tag="span"
      >
        Answer
      </router-link>
      <span class="active">Video</span>
      <router-link :to="`/app/vidgen/${id}/${step_id}/edit/logic`" tag="span">
        Funnel
      </router-link>
    </div>
    <div class="content-wrapper">
      <div class="thumbnail-wrapper">
        <img :src="getUrl(thumbImage)" v-if="thumbImage" />
        <div class="video-button-group">
          <button
            class="btn circle-button"
            :class="currentSelection == 'cc' ? 'active' : ''"
            @click="handleSelection('cc')"
            title="Add Subtitles"
          >
            CC
          </button>
          <button
            class="btn circle-button ml-2"
            :class="currentSelection == 'note' ? 'active' : ''"
            @click="handleSelection('note')"
            title="Add Note"
          >
            <img src="/images/icons/message.svg" />
          </button>
          <button
            class="btn circle-button ml-2"
            @click="handleDownload"
            title="Download Video"
          >
            <img src="/images/icons/download.svg" />
          </button>
          <button
            class="btn circle-button ml-2"
            @click="handleReplaceThumbImage"
            title="Replace Video"
          >
            <img src="/images/icons/replace.svg" />
          </button>
        </div>
      </div>
      <div class="fit-video-wrapper">
        <span>Fit videos</span>
        <b-form-checkbox
          v-model="fitVideo"
          name="fit-video"
          switch
          size="lg"
        ></b-form-checkbox>
      </div>
      <hr class="mt-4 w-100" />
      <div
        class="note-wrapper mt-3 d-flex align-items-center justify-content-between mb-3"
        v-if="currentSelection == 'cc'"
      >
        <h5 class="overlay-label mb-0">CC ({{ getDuration() }})</h5>
        <a href="javascript:;" class="btn btn-sm btn-primary" @click="createCC">
          <fa-icon :icon="['fas', 'plus']" />
        </a>
      </div>
      <perfect-scrollbar v-if="currentSelection == 'cc'">
        <div class="cc-wrapper">
          <cc-item
            :key="`cc-item-${index}`"
            :item="item"
            @onRemoveCCItem="handleRemoveCCItem"
            v-for="(item, index) in cc_items"
          />
        </div>
      </perfect-scrollbar>

      <div class="note-wrapper mt-3 px-2" v-if="currentSelection == 'note'">
        <h5 class="overlay-label">Note</h5>
        <b-form-textarea
          id="overlay-text"
          v-model="video_note"
          placeholder="Add notes here"
          rows="4"
        ></b-form-textarea>
      </div>
    </div>
    <div class="vidpopup-step-button-group">
      <div>
        <button
          type="button"
          class="btn circle-button btn-save mr-2"
          @click="saveStep()"
        >
          <fa-icon :icon="['fas', 'check']" />
        </button>
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
#step-flow-video {
  height: 100%;
  display: flex;
  flex-direction: column;
  .ps {
    max-height: 300px;
  }
  .content-wrapper {
    height: calc(100% - 50px);
    overflow-y: auto;
    padding-bottom: 50px;
    .thumbnail-wrapper {
      height: 200px;
      width: 100%;
      border-radius: 30px;
      overflow: hidden;
      position: relative;
      img {
        width: 100%;
        height: 100%;
      }
      .video-button-group {
        position: absolute;
        bottom: 0;
        right: 0;
        border-radius: 30px;
        padding: 10px;
        .circle-button {
          background-color: #f0f1f3;
          color: #444;
          font-size: 1rem;
          border-radius: 50%;
          padding: 0px;
          width: 40px;
          height: 40px;

          &:hover {
            background-color: #1998e4;
            color: white;
            img {
              filter: invert(1);
            }
          }
          &.active {
            background-color: #1998e4;
            color: white !important;
            img {
              filter: invert(1);
            }
          }
          img {
            width: 15px;
            height: 15px;
          }
        }
      }
    }
    .fit-video-wrapper {
      display: flex;
      align-items: center;
      margin-top: 30px;
      span {
        font-size: 1.1rem;
        font-weight: 700;
        margin-right: 10px;
      }
    }
    .overlay-label {
      font-size: 1.1rem;
      font-weight: 700;
    }
    textarea {
      background-color: #f0f1f3;
      padding: 1rem;
      resize: none;
      border-radius: 0.5rem;
      border: none;
    }
  }
}
</style>

<script>
import { STEP, STEP_UPDATED } from '@/services/store/vidpopup.module';
import { mapGetters } from 'vuex';
import ccItem from '@/views/components/cc-item.vue';

export default {
  components: { ccItem },
  name: 'step-video',
  computed: {
    ...mapGetters(['vidpop', 'step', 'currentUser', 'duration'])
  },
  watch: {
    fitVideo: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        fit_video: newVal
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    video_note: function(newVal, oldVal) {
      this.$store.dispatch(STEP, {
        ...this.step,
        video_note: newVal
      });
      this.$store.dispatch(STEP_UPDATED, true);
    },
    cc_items: {
      handler: function(newVal, oldVal) {
        this.$store.dispatch(STEP, {
          ...this.step,
          video_cc: JSON.stringify(newVal)
        });
        this.$store.dispatch(STEP_UPDATED, true);
      },
      deep: true
    }
  },
  data() {
    return {
      fitVideo: false,
      id: '',
      thumbImage: '',
      file: null,
      user_id: 0,
      step_id: '',
      showNote: false,
      video_note: '',
      currentSelection: 'cc',
      cc_items: [],
      awsUrl: 'https://vidpopup.s3.amazonaws.com/'
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
    this.awsUrl = process.env.MIX_CDN_URL;
    this.id = this.$route.params.id;
    this.step_id = this.$route.params.step;
    this.user_id = this.currentUser.id;
    this.thumbImage = this.step.thumb_url;
    this.video_note = this.step.video_note;

    if (this.step.video_cc && typeof this.step.video_cc === 'string') {
      this.cc_items = JSON.parse(this.step.video_cc);
    } else {
      this.cc_items = this.step.video_cc;
    }

    this.fitVideo = this.step.fit_video ? true : false;
  },
  methods: {
    handleReplaceThumbImage() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.step_id}/manage?index=${this.step.index}`
      });
    },
    handleDownload() {
      let link = document.createElement('a');
      link.download = 'download.mp4';
      link.href = this.getUrl(this.step.video_url);
      link.target = '_blank';
      link.click();
    },
    handleNext() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.step_id}/edit/logic`
      });
    },
    handleBack() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.step_id}/edit/answer`
      });
    },
    handleSelection(selection) {
      this.currentSelection = selection;
    },
    createCC() {
      const len = this.cc_items.length;
      const last = len === 0 ? '00:00' : this.cc_items[len - 1].end;
      this.cc_items = [
        ...this.cc_items,
        { description: '', start: last, end: last }
      ];
    },
    handleRemoveCCItem(info) {
      const { item } = info;
      var selectedItem = this.cc_items.find(x => x === item);
      if (selectedItem) {
        let index = this.cc_items.indexOf(selectedItem);
        this.cc_items.splice(index, 1);
      }
    },
    saveStep() {
      const validCheck = this.$func.validateStep(this.step);
      if (!validCheck.state) {
        this.$func.makeToast(this, 'Error', validCheck.msg, 'danger');
        return;
      }
      const loader = this.$loading.show();
      axios
        .post('/api/update-step', this.step)
        .then(res => {
          if (res && res.status === 200) {
            this.$func.makeToast(
              this,
              'Notice',
              'Current step is saved!',
              'success'
            );
          } else {
            this.$func.makeToast(this, 'Error', res.data.error, 'danger');
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot save the current step!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    getDuration() {
      return this.$func.formatTime(this.duration);
    },
    getUrl(url) {
      if (url[0] == 'u') // uploaded file
        return `${this.awsUrl}${url}`;
      return url;
    }
  }
};
</script>
