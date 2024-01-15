<template>
  <div id="vidpopup-flow-embed">
    <div class="flow-headers">
      <router-link :to="`/app/vidgen/${id}/settings/link`" tag="span">
        Link
      </router-link>
      <router-link :to="`/app/vidgen/${id}/settings/widget`" tag="span">
        Widget
      </router-link>
      <span class="active">Embed</span>
    </div>
    <div class="content-wrapper">
      <h5 class="title">
        Embed Your VidGen Inside A Webpage on Your Website:
      </h5>
      <h6 class="text-grey">
        Use this code to embed your vidgen anywhere on your website:
      </h6>
      <div class="mt-4">
        <div class="embed-box">
          <pre class="d-flex p-0">
            <pre class="d-flex">
              <code class="language-html" style="white-space: pre-wrap;"><span><span>&lt;iframe </span><span style="color: #a32451;">src</span><span>=</span><span style="color: rgb(230, 219, 116);">"{{originUrl}}/live/{{vidpop.code}}"</span><span>
</span></span><span><span></span><span style="color: #a32451;">allow</span><span>=</span><span style="color: rgb(230, 219, 116);">"camera *; microphone *; autoplay *; encrypted-media *; fullscreen *; display-capture *;"</span><span>
</span></span><span><span></span><span style="color: #a32451;">width</span><span>=</span><span style="color: rgb(230, 219, 116);">"100%"</span><span>
</span></span><span><span></span><span style="color: #a32451;">height</span><span>=</span><span style="color: rgb(230, 219, 116);">"600px"</span><span>
</span></span><span><span></span><span style="color: #a32451;">style</span><span>=</span><span style="color: rgb(230, 219, 116);">"border: none; border-radius: 24px"</span><span>
</span></span><span>&gt;
</span><span>&lt;/iframe&gt;
</span><span>
</span></code>
            </pre>
          </pre>
          <b-button class="copy-code-btn mt-3" @click="handleCopy">{{
            buttonLabel
          }}</b-button>
        </div>
      </div>

      <div class="mt-4 w-100">
        <button class="btn btn-large btn-success w-100" @click="saveVidpop()">
          DONE
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
#vidpopup-flow-embed {
  height: 100%;
  display: flex;
  flex-direction: column;
  .ps {
    height: 550px;
  }
  .content-wrapper {
    overflow-y: auto;
    padding: 0px 10px 50px 0px;
    .embed-box {
      min-height: 200px;
      color: #fff;
      position: relative;
      width: 100%;
      background: #1998e4;
      border-radius: 20px;
      padding: 10px;
      height: auto;
      pre {
        margin: 0px 0px 16px;
        overflow-x: auto;
        overflow-wrap: break-word;
        display: block;
        overflow-x: auto;
        padding: 0.5em;
        background: #1998e4;
        color: rgb(248, 248, 242);
        .link {
          white-space: pre-wrap;
        }
      }
    }
    .copy-code-btn {
      border-radius: 20px;
      background: #1998e4;
      color: #fff;
      position: absolute;
      right: 10px;
      bottom: 10px;
    }
    .event {
      a {
        color: gray;
        font-weight: 700;
        text-decoration: underline;
      }
    }
  }
}
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
export default {
  name: 'vidpopup-embed',
  computed: {
    ...mapGetters(['vidpop', 'currentUser'])
  },
  data() {
    return {
      id: '',
      originUrl: '',
      buttonLabel: 'Copy Code',
      embedCode: ''
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
    this.embedCode = `<iframe src="${this.originUrl}/live/${this.vidpop.code}" allow="camera *; microphone *; autoplay *; encrypted-media *; fullscreen *; display-capture *;" width="100%" height="600px" />`;
  },
  methods: {
    handleCopy() {
      const el = document.createElement('textarea');
      el.value = this.embedCode;
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);

      this.buttonLabel = 'Copied';
    },
    saveStep() {
      // validate
      const validCheck = this.$func.validateStep(this.step);
      if (!validCheck.state) {
        this.$func.makeToast(this, 'Error', validCheck.msg, 'danger');
        return;
      }
      const loader = this.$loading.show();
      axios
        .post('/api/update-step', this.step)
        .then(res => {})
        .catch(err => {})
        .finally(() => {
          loader && loader.hide();
        });
    },
    saveVidpop() {
      const loader = this.$loading.show();
      axios
        .post('/api/vidpop', this.vidpop)
        .then(res => {
          if (res && res.status === 200) {
            this.$func.makeToast(
              this,
              'Notice',
              'Current step is saved!',
              'success'
            );
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot save the current step!',
              'danger'
            );
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
    }
  }
};
</script>
