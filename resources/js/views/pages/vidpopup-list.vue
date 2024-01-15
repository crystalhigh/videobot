<template>
  <div id="page-vidpopup-list">
    <b-container fluid class="h-100">
      <h2>Videos</h2>
      <div class="search-wrapper">
        <b-form-input
          type="search"
          id="vidpop-search"
          class="form-control mt-4"
          placeholder="Search by title"
          v-debounce:300ms="updateFilter"
        />
        <fa-icon :icon="['fas', 'search']" class="search-icon" />
      </div>
      <b-row class="h-100 mt-4">
        <b-col md="4" v-for="(item, idx) in items" :key="`vidpop-item-${idx}`" class="mt-3 mb-3">
          <div class="vidpopup-item">
            <img
              :src="item.thumb ? getThumbUrl(item.thumb) : ''"
              @error="defaultImage"
              class="vidpopup-item-image"
              @click="onPreviewItem(item)"
            />
            <div class="info-section">
              <div class="creator-name-wrapper">
                <div class="d-flex align-items-center">
                  <span class="creator-avatar">
                    {{ getAbbreviation(item.user.name) }}
                  </span>
                  <span class="creator-name">{{ item.user.name }}</span>
                </div>
                <div class="vidpop-share">
                  <div class="vidpop-copy-icon">
                    <button class="btn btn-sm btn-primary" @click="onShare(item, idx)" v-if="!item.publisher_vidpopup_last">Promote Now!</button>
                    <button class="btn btn-sm btn-success" @click="onPromoteTools(item)" v-else-if="item.publisher_vidpopup_last && item.publisher_vidpopup_last.status=='Approved'">Promote Tools</button>
                    <button class="btn btn-sm btn-secondary" disabled v-else-if="item.publisher_vidpopup_last && item.publisher_vidpopup_last.status=='Pending'">Pending!</button>
                    <button class="btn btn-sm btn-danger" disabled v-else-if="item.publisher_vidpopup_last && item.publisher_vidpopup_last.status=='Rejected'">Rejected!</button>
                  </div>
                  <span class="ml-1">{{ item.publisher_vidpopup_cnt }}</span>
                </div>
              </div>
              <h5 class="vidpop-title">{{ item.name }}</h5>
              <div class="d-flex align-items-center">
                <div class="d-flex align-items-center gap-1 text-white">
                  <fa-icon :icon="['fas', 'hand-pointer']" />
                  <span class="ml-1">{{ calculateMetrics(item.publisher_vidpopup, "click") }}</span>
                </div>
                <div class="d-flex align-items-center gap-1 ml-4 text-white">
                  <fa-icon :icon="['fas', 'eye']" />
                  <span class="ml-1">{{ calculateMetrics(item.publisher_vidpopup, "view") }}</span>
                </div>
                <div class="gap-1 ml-4 text-white cost-per-lead">
                  <h6 class="vidpop-title">${{ item.cost }}/Lead</h6>
                </div>
              </div>
            </div>
          </div>
        </b-col>
      </b-row>
    </b-container>
    <b-modal
      id="copy-code-modal"
      hide-footer
      :title="selectedItem ? selectedItem.name : 'Promote'"
      centered
      ref="copyCodeModal"
      size="md">
      <div class="d-block text-center">
        <h3>Promote Now!</h3>
      </div>
      <div>
        <b-form-textarea
          v-model="description"
          placeholder="Explain how you will promote the offer"
          class="copy-code-input mt-3"
          style="font-size: 16px; border-radius: 4px;"
          rows="4"
          max-rows="4"
        />
      </div>
      <div class="d-flex send-request-widget-link" v-for="(siteurl, index) in arrWebsiteUrl" :key="index">
        <b-form-input
          type="text"
          v-model="arrWebsiteUrl[index]"
          placeholder="Enter your website url to share."
          class="copy-code-input mt-3 py-3"
          style="font-size: 16px; border-radius: 4px;"
        />
        <b-button class="mt-3 px-2 text-center" variant="danger" @click="removeLink(index)"><fa-icon :icon="['fas', 'minus']" class="minus-icon" /></b-button>
      </div>
      <b-button class="mt-3 px-2 text-center" variant="success" @click="addLink"><fa-icon :icon="['fas', 'plus']" class="plus-icon" /> Add Link</b-button>
      <div class="d-flex justify-content-end copy-modal">
        <b-button class="mt-3 px-2 text-center" variant="success" @click="shareWidget">Request Now</b-button>
        <b-button class="mt-3 ml-2 px-2" @click="hideCopyModal">Close</b-button>
      </div>
    </b-modal>
    <b-modal
      id="promote-tools-modal"
      hide-footer
      title="Promote Tools"
      centered
      ref="promoteTollsModal"
      size="md">
      <div>
        <span class="promote-tools-modal-span fw-700">Widget Code:</span>
      </div>
      <div>
        <b-form-textarea
          v-model="widgetCode"
          readonly
          class="copy-code-input mt-2"
          style="font-size: 16px; border-radius: 4px;"
          rows="4"
          max-rows="4"
        />
      </div>
      <div class="d-flex mt-3 align-items-center gap-12">
        <span class="promote-tools-modal-span">Position:</span>
        <b-form-select
          v-model="widgetPosition"
          :options="widgetPositionOptions"
        ></b-form-select>
      </div>
      <div class="d-flex mt-3 align-items-center gap-12">
        <span class="promote-tools-modal-span">Widget Style:</span>
        <b-form-select
          v-model="widgetStyle"
          :options="widgetStyleOptions"
        ></b-form-select>
      </div>
      <div class="d-flex mt-3 align-items-center gap-12">
        <span class="promote-tools-modal-span">Background Color:</span>
        <input
          type="color"
          ref="colorRef"
          class="w-100"
          style="border-radius:4px; overflow: hidden;cursor: pointer;"
          v-model="widgetBorderColor"
        />
      </div>
      <div class="d-flex mt-3 align-items-center gap-12" style="position: relative;">
        <span class="promote-tools-modal-span">Text:</span>
        <b-form-input
          type="text"
          v-model="widgetText"
          placeholder="Enter your widget text."
          class="py-3"
          style="border-radius: 4px;"
          ref="widgetTextRef"
        />
        <a @click="showEmoji" class="emoji-icon"><fa-icon :icon="['fas', 'smile']" /></a>
      </div>
      <VEmojiPicker @select="selectEmoji" v-if="emoji" class="emoji-modal"/>
      <div class="mt-3">
        <span class="promote-tools-modal-span fw-700">OR:</span>
      </div>
      <div>
        <span class="promote-tools-modal-span fw-700">Your Direct Promote Link:</span>
      </div>
      <div>
        <b-form-input
          type="text"
          v-model="widgetLink"
          readonly
          class="mt-2 py-3"
          style="border-radius: 4px;"
        />
      </div>
      <div class="d-flex justify-content-end copy-modal">
        <b-button class="mt-3 px-2 text-center" variant="primary" @click="shareCode">Copy Code</b-button>
        <b-button class="mt-3 px-2 text-center ml-2" variant="primary" @click="shareLink">Copy Link</b-button>
        <b-button class="mt-3 ml-2 px-2" variant="danger" @click="hideCodeModal">Close</b-button>
      </div>
    </b-modal>
  </div>
</template>
<style lang="scss" moduled>
.emoji-icon {
  color: orange;
  font-size: 20px;
  position: absolute;
  top: 3px;
  right: 8px;
  &:hover {
    cursor: pointer;
    opacity: .7;
  }
}
.emoji-modal {
    position: absolute;
    right: -330px;
    top: -62px;
  }
#page-vidpopup-list {
  padding: 20px;
  .search-wrapper {
    position: relative;
    width: 450px;
    #vidpop-search {
      padding-left: 40px;
    }
    .search-icon {
      position: absolute;
      left: 15px;
      color: #a1a1a1;
      top: 12px;
    }
  }
  .vidpopup-item {
    width: 100%;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    height: 300px;
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      cursor: pointer;
      &:hover {
        opacity: 0.85;
      }
    }
    .info-section {
      position: absolute;
      bottom: 0px;
      left: 0px;
      right: 0px;
      padding: 15px 20px;
      background-color: rgba(0, 0, 0, 0.55);
      .creator-name-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        .creator-avatar {
          width: 35px;
          height: 35px;
          border-radius: 10px;
          overflow: hidden;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #1998e4;
          color: white;
          cursor: pointer;
          font-size: 14px;
        }
        .creator-name {
          color: white;
          margin-left: 10px;
          font-weight: 500;
          font-size: 14px;
        }
        .vidpop-share {
          color: white;
          cursor: pointer;
          &:hover {
            opacity: 0.75;
          }
        }
      }
      .vidpop-title {
        margin-top: 10px;
        color: white;
      }
      .vidpop-copy-icon {
        display: contents;
      }
      .cost-per-lead {
        width: 100%;
        text-align: right;
      }
    }
  }
  .widget-title {
    font-size: 16px;
    font-weight: 600;
  }
  .widget {
    cursor: pointer;
    width: 40px;
    height: 40px;
  }
  .copy-code-input {
    font-size: 20px !important;
    border-radius: 16px;
    height: auto !important;
  }
}
.send-request-widget-link {
  gap: 12px;
}
.promote-tools-modal-span {
  min-width: 140px;
  font-size: 16px;
}
.fw-700 {
  font-weight: 700;
}
.gap-12 {
  gap: 12px;
}
</style>

<script>
import Swal from 'sweetalert2';
import { mapGetters } from 'vuex';
import { VEmojiPicker } from 'v-emoji-picker';

export default {
  name: 'vidpopup-list',
  computed: {
    ...mapGetters(['vidpop', 'currentUser'])
  },
  components: {
    VEmojiPicker
  },
  watch: {
    widgetPosition: function(newVal, oldVal) {
      this.setWidgetCode();
    },
    widgetStyle: function(newVal, oldVal) {
      this.setWidgetCode();
    },
    widgetBorderColor: function(newVal, oldVal) {
      this.setWidgetCode();
    },
    widgetText: function(newVal, oldVal) {
      this.setWidgetCode();
    },
  },
  data() {
    return {
      items: [],
      filtered: [],
      loading: false,
      awsUrl: 'https://vidpopup.s3.amazonaws.com/',
      page: 1,
      websiteUrl: '',
      description: '',
      selectedItem: null,
      arrWebsiteUrl: [''],
      selectedVidgenIndex: -1,
      widgetCode: '',
      widgetPosition: 'right',
      widgetPositionOptions: [
        {value: 'right', text: 'Right'},
        {value: 'left', text: 'Left'}
      ],
      widgetStyle: 'circle',
      widgetStyleOptions: [
        {value: 'circle', text: 'Circle'},
        {value: 'v-rect', text: 'Square'}
      ],
      widgetBorderColor: '#1998e4',
      widgetText: '',
      widgetLink: '',
      promoteItem: null,
      emoji: false,
    };
  },
  beforeMount() {
    if (!this.currentUser || (this.currentUser && this.currentUser.role != 'publisher')) {
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
  async mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;
    const loader = this.$loading.show();
    try {
      const { data } = await axios.get('/api/vidpop-adv?page=1');
      this.items = data;
      this.filtered = data;
    } catch (err) {
      console.log(err);
    }
    loader && loader.hide();
    this.loading = false;
  },
  methods: {
    showEmoji() {
      this.emoji = !this.emoji;
    },
    selectEmoji(emoji) {
      const ref = this.$refs.widgetTextRef;
      const start = this.widgetText.substring(0, ref.selectionStart)
      const end = this.widgetText.substring(ref.selectionStart)
      this.widgetText = start + emoji.data + end;
      this.emoji = false;
    },
    setWidgetCode() {
      const thumb = this.getThumbUrl(this.promoteItem.steps.length ? this.promoteItem.steps.filter(val => val.index == 1)[0].video_url : '');
      this.widgetCode = `<link rel="stylesheet" href="${
          process.env.MIX_APP_URL
        }/embed/embed.css" />
        <script>
          window.VIDPOP_EMBED_CONFIG = {
            url: "${process.env.MIX_APP_URL}/live/${this.promoteItem.code}?vidgenID=${this.promoteItem.publisher_vidpopup_last.id}",
            thumb: "${thumb}",
            widget: "${this.widgetStyle}",
            option: {
              background: "${this.widgetBorderColor}",
              position: "${this.widgetPosition}",
              text: "${this.widgetText}",
            },
            main: "${process.env.MIX_APP_URL}",
            vidgenID: "${this.promoteItem.publisher_vidpopup_last.id}"
          }
        <\/script>
        <script src="${
          process.env.MIX_APP_URL
        }/embed/embed2.js"><\/script>`;
    },
    shareCode() {
      navigator.clipboard.writeText(this.widgetCode);
      this.$func.makeToast(
        this,
        'Notice',
        'Code is copied to clipboard',
        'success'
      );
      this.$bvModal.hide('promote-tools-modal');
    },
    shareLink() {
      navigator.clipboard.writeText(this.widgetLink);
      this.$func.makeToast(
        this,
        'Notice',
        'Link is copied to clipboard',
        'success'
      );
      this.$bvModal.hide('promote-tools-modal');
    },
    hideCodeModal() {
      this.$bvModal.hide('promote-tools-modal');
    },
    formatData() {
      this.widgetPosition = 'right';
      this.widgetStyle = 'circle';
      this.widgetBorderColor = '#1998e4';
      this.widgetText = '';
      this.widgetLink = '';
    },
    onPromoteTools(item) {
      this.emoji = false;
      this.promoteItem = item;
      this.formatData();
      this.setWidgetCode();
      this.widgetLink = `${process.env.MIX_APP_URL}/live/${item.code}?vidgenID=${this.promoteItem.publisher_vidpopup_last.id}`;
      this.$bvModal.show('promote-tools-modal');
    },
    addLink() {
      this.arrWebsiteUrl.push('');
    },
    removeLink(index) {
      this.arrWebsiteUrl.splice(index, 1);
    },
    hideCopyModal() {
      this.$bvModal.hide('copy-code-modal');
    },
    defaultImage(e) {
      e.target.src = '/images/placeholder.png';
    },
    updateFilter() {},
    getAbbreviation(name) {
      const namelst = name.split(' ').map(n => n.charAt(0).toUpperCase());
      return namelst.join('');
    },
    shareWidget() {
      let flagPass = true
      this.arrWebsiteUrl.map(val => {
        let tmp = val.replaceAll(' ', '');
        if (tmp == '')
          flagPass = false
      });
      if (!flagPass) {
        this.$func.makeToast(
          this,
          'Warning',
          'You should enter your website url!',
          'warning'
        );
        return;
      }
      let item = this.selectedItem
      this.websiteUrl = this.arrWebsiteUrl.join(";");
      let description = this.description.split(" ").join("");
      if (description == "") {
        this.$func.makeToast(
          this,
          'Warning',
          'You should enter your description!',
          'warning'
        );
        return;
      }
      const loader = this.$loading.show();
      axios
        .get(`/api/publish-widget?id=${item.id}&url=${this.websiteUrl}&description=${this.description}`)
        .then(res => {
          if (res && res.status == 200) {
            if (res.data.error) {
              this.$func.makeToast(
                this,
                'Notice',
                res.data.error,
                'danger'
              );
            } else {
              this.$func.makeToast(
                this,
                'Notice',
                'Sent copy request to Advertiser!',
                'success'
              );
            }
            this.items[this.selectedVidgenIndex].publisher_vidpopup_last = res.data.pv_last;
          }
          // let wPos = this.widgetPosition == 0 ? 'right' : 'left';
          // let vidpopup_id = res.data.published.vidpopup_id
          // let copiedItem = this.items.filter(val => val.id == vidpopup_id)[0]
          // copiedItem.publisher_vidpopup = [...copiedItem.publisher_vidpopup, res.data.published]
          // item.publisher_vidpopup_cnt = res.data.published_count
          // let video_url = this.getThumbUrl(res.data.video_url)
          // const code = `<link rel="stylesheet" href="${
          //   process.env.MIX_APP_URL
          // }/embed/embed.css" />
          // <script>
          //   window.VIDPOP_EMBED_CONFIG = {
          //     url: "${process.env.MIX_APP_URL}/live/${item.code}?vidgenID=${res.data.pv_id}",
          //     thumb: "${video_url}",
          //     widget: "circle",
          //     option: {
          //       background: "${widget?.color || '#1998e4'}",
          //       position: "${widget?.position || wPos}",
          //       text: "${widget?.text || ''}",
          //     },
          //     site: "${this.websiteUrl}"
          //   }
          // <\/script>
          // <script src="${
          //   process.env.MIX_APP_URL
          // }/embed/embed2.js"><\/script>`;
          // loader && loader.hide();
          // this.$func.makeToast(
          //   this,
          //   'Notice',
          //   'Code is copied to clipboard',
          //   'success'
          // );
          // // const el = document.createElement('textarea');
          // // el.value = code;
          // // document.body.appendChild(el);
          // // el.select();
          // // document.execCommand('copy');
          // // document.body.removeChild(el);
          // navigator.clipboard.writeText(code);
        })
        .catch(err => {
          console.log(err);
          this.$func.makeToast(
            this,
            'Error',
            'This vidgen is not ready!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
          this.hideCopyModal();
        });
    },
    onShare(item, idx) {
      // get widget code
      this.websiteUrl = ""
      this.description = ""
      this.selectedItem = item
      this.arrWebsiteUrl = [''];
      this.selectedVidgenIndex = idx;
      this.$bvModal.show('copy-code-modal');
    },
    onPreviewItem(item) {
      console.log(item);
      const routeData = this.$router.resolve({
        path: `/live/${item.code}?preview=1`
      });
      window.open(routeData.href, '_blank');
    },
    calculateMetrics(pv_item, status) {
      let result = 0;
      if (status == 'click')
        pv_item.map(metric => {
          result += metric.metrics && metric.metrics.length ? metric.metrics.filter(v => v.status == 'click').length : 0
        })
      else
        pv_item.map(metric => {
          result += metric.metrics && metric.metrics.length ? metric.metrics.filter(v => (v.status == 'click' || v.status == 'view')).length : 0
        })
      return result;
    },
    getThumbUrl(url) {
      if (url[0] == 'u') // uploaded file
        return `${this.awsUrl}${url}`;
      return url;
    }
  }
};
</script>
