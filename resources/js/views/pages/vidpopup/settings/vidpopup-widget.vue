<template>
  <div id="vidpopup-flow-widget">
    <div class="flow-headers">
      <router-link :to="`/app/vidgen/${id}/settings/link`" tag="span">
        Link
      </router-link>
      <span class="active">Widget</span>
      <router-link :to="`/app/vidgen/${id}/settings/embed`" tag="span">
        Embed
      </router-link>
    </div>
    <div class="content-wrapper">
      <h5 class="title">
        Grab the Attention of Your Website Visitors Using the VidGen Widget
      </h5>
      <h5 class="title mt-4">
        Widget Style :
      </h5>
      <b-row class="mt-2 ml-0 mr-0">
        <b-col md="12" class="pl-0 pr-0">
          <span @click="handleWidget(0)">
            <widget-a class="widget" :selected="widget === 0" />
          </span>
          <span @click="handleWidget(1)" class="ml-2">
            <widget-b class="widget" :selected="widget === 1" />
          </span>
        </b-col>
      </b-row>

      <b-row class="mt-2 ml-0 mr-0">
        <b-col md="6" class="pl-0">
          <h5 class="title mt-4 ml-0 mr-0">
            Position
          </h5>
          <span @click="handlePosition(0)">
            <position-a class="widget" :selected="position === 0" />
          </span>
          <span @click="handlePosition(1)" class="ml-2">
            <position-b class="widget" :selected="position === 1" />
          </span>
        </b-col>
        <b-col md="6">
          <h5 class="title mt-4">
            Color
          </h5>
          <input
            type="color"
            ref="colorRef"
            style="border-radius:10px; overflow: hidden;cursor: pointer;"
            v-model="widgetBorderColor"
          />
        </b-col>
      </b-row>

      <b-row class="mt-4 ml-0 mr-0">
        <b-col md="12" class="pl-0 pr-0">
          <b-form-group
            label="Overlay Text"
            class="widget-text"
            label-for="vidpop-widget-text"
          >
            <b-form-input
              id="vidpop-widget-text"
              v-model="text"
              type="text"
              class="borderless-text-field"
            ></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>

      <b-row class="mt-2 ml-0 mr-0">
        <b-col md="12" class="pl-0 pr-0">
          <b-button
            class="btn mt-4 text-white get-code-btn"
            @click="handleCode"
          >
            Get The Code
          </b-button>
        </b-col>
      </b-row>
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

<style lang="scss" moduled>
input[type='color'] {
  -webkit-appearance: none;
  border: none;
  width: 32px;
  height: 32px;
  width: 40px;
  height: 40px;
  border-radius: 7px;
  display: flex;
}
input[type='color']::-webkit-color-swatch-wrapper {
  padding: 0;
}
input[type='color']::-webkit-color-swatch {
  border: none;
}
#vidpopup-flow-widget {
  height: 100%;
  display: flex;
  flex-direction: column;
  .ps {
    height: 550px;
  }
  .content-wrapper {
    overflow-y: auto;
    padding: 0px 10px 50px 0px;
    .color-panel {
      background: #1998e4;
      width: 40px;
      height: 40px;
      border-radius: 7px;
      display: flex;
    }
    .get-code-btn {
      border-radius: 20px;
      background-color: #1998e4;
      border-color: #1998e4;
      margin-left: 5px;
    }
    .widget {
      cursor: pointer;
      width: 40px;
      height: 40px;
    }
  }
  .widget-text.form-group label {
    margin-bottom: 0px;
    color: #110111;
    font-weight: 600;
  }
}
</style>

<script>
import axios from 'axios';
import PositionA from '@/views/components/icon/PositionA.vue';
import PositionB from '@/views/components/icon/PositionB.vue';
import WidgetA from '@/views/components/icon/WidgetA.vue';
import WidgetB from '@/views/components/icon/WidgetB.vue';
import { mapGetters } from 'vuex';
import { VIDPOP, VIDPOP_UPDATED } from '@/services/store/vidpopup.module';
export default {
  components: { WidgetA, WidgetB, PositionA, PositionB },
  name: 'vidpopup-widget',
  computed: {
    ...mapGetters(['vidpop', 'step', 'currentUser'])
  },
  data() {
    return {
      widget: 0,
      position: 0,
      id: '',
      text: '',
      widgetBorderColor: '#1998e4'
    };
  },
  watch: {
    text: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        widget: {
          ...this.vidpop.widget,
          text: newVal
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    position: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        widget: {
          ...this.vidpop.widget,
          position: newVal === 1 ? 'left' : 'right'
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    widget: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        widget: {
          ...this.vidpop.widget,
          style: this.getWidget()
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    },
    widgetBorderColor: function(newVal, oldVal) {
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        widget: {
          ...this.vidpop.widget,
          color: newVal
        }
      });
      this.$store.dispatch(VIDPOP_UPDATED, true);
    }
  },
  mounted() {
    this.id = this.$route.params.id;
    this.text = this.vidpop.widget.text;
    this.widgetBorderColor = this.vidpop.widget.color;
    this.position = this.vidpop.widget.position === 'left' ? 1 : 0;
    this.widget = this.getWidgetValue(this.vidpop.widget.style);
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
  methods: {
    handleBack() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/settings/email`
      });
    },
    handleNext() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/settings/embed`
      });
    },
    handleWidget(item) {
      this.widget = item;
    },
    handlePosition(item) {
      this.position = item;
    },
    getPosition() {
      if (this.position === 1) {
        return 'left';
      }
      return 'right';
    },
    getWidget() {
      if (this.widget === 0) {
        return 'circle';
      } else if (this.widget === 1) {
        return 'v-rect';
      } else if (this.widget === 2) {
        return 'h-rect';
      }
    },
    getWidgetValue(v) {
      if (v === 'circle') {
        return 0;
      } else if (v === 'v-rect') {
        return 1;
      } else if (v === 'h-rect') {
        return 2;
      }
    },
    handleCode() {
      const loader = this.$loading.show();
      axios
        .get(`/api/first-step?v_id=${this.id}`)
        .then(res => {
          if (res && res.status === 200) {
            const code = `<link rel="stylesheet" href="${
              process.env.MIX_APP_URL
            }/embed/embed.css" />
              <script>
                window.VIDPOP_EMBED_CONFIG = {
                  url: "${process.env.MIX_APP_URL}/live/${this.vidpop.code}",
                  thumb: "${process.env.MIX_CDN_URL}${res.data.video_url}",
                  widget: "${this.getWidget()}",
                  option: {
                    background: "${this.widgetBorderColor}",
                    position: "${this.getPosition()}",
                    text: "${this.text}",
                  }
                }
              <\/script>
              <script src="${
                process.env.MIX_APP_URL
              }/embed/embed2.js"><\/script>`;
            loader && loader.hide();
            this.$func.makeToast(
              this,
              'Notice',
              'Code is copied to clipboard',
              'success'
            );
            const el = document.createElement('textarea');
            el.value = code;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'This VidGen is not ready!',
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
