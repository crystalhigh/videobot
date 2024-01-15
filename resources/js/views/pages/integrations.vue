<template>
  <div id="page-integration">
    <div class="page-header">
      <h5>AR Integrations</h5>
      <img src="/images/icons/integration.svg" />
    </div>
    <div class="type-wrapper">
      <button
        type="button"
        class="btn btn-integrate"
        :class="selected === 'aweber' ? 'active' : ''"
        @click="handleSelect('aweber')"
        :disabled="isDisabled('aweber')"
      >
        <img src="/images/logos/aweber.svg" />
        Aweber
      </button>
      <button
        type="button"
        class="btn btn-integrate ml-2"
        :class="selected === 'getresponse' ? 'active' : ''"
        @click="handleSelect('getresponse')"
        :disabled="isDisabled('getresponse')"
      >
        <img src="/images/logos/getresponder.svg" />
        Getresponse
      </button>
      <button
        type="button"
        class="btn btn-integrate ml-2"
        :class="selected === 'mailchimp' ? 'active' : ''"
        @click="handleSelect('mailchimp')"
        :disabled="isDisabled('mailchimp')"
      >
        <img src="/images/logos/mailchimp.svg" />
        Mailchimp
      </button>
      <button
        type="button"
        class="btn btn-integrate ml-2"
        :class="selected === 'zapier' ? 'active' : ''"
        @click="handleSelect('zapier')"
        :disabled="isDisabled('zapier')"
      >
        <img src="/images/logos/zapier.svg" />
        Zapier
      </button>
      <button
        type="button"
        class="btn btn-integrate ml-2"
        :class="selected === 'pabbly' ? 'active' : ''"
        @click="handleSelect('pabbly')"
        :disabled="isDisabled('pabbly')"
      >
        <img src="/images/logos/pabbly.png" />
        Pabbly
      </button>
      <button
        type="button"
        class="btn btn-integrate ml-2"
        :class="selected === 'sendinblue' ? 'active' : ''"
        @click="handleSelect('sendinblue')"
        :disabled="isDisabled('sendinblue')"
      >
        <img src="/images/logos/sendinblue.png" />
        SendInBlue
      </button>
      <button
        type="button"
        class="btn btn-integrate ml-2"
        :class="selected === 'csv' ? 'active' : ''"
        @click="handleSelect('csv')"
      >
        <img src="/images/logos/csv.svg" />
        CSV File
      </button>
    </div>
    <div class="h-100">
      <b-row class="h-100">
        <b-col md="6">
          <div class="w-100 h-100 d-flex align-items-center">
            <div class="video-container">
              <iframe
                :src="url"
                frameborder="0"
                allow="autoplay; encrypted-media"
                allowfullscreen
                v-if="url"
              ></iframe>
            </div>
          </div>
        </b-col>
        <b-col md="6">
          <div class="integrate-body h-100 p-2">
            <div class="integrate-form-wrapper" v-if="selected === 'aweber'">
              <template v-if="isLoadingIntegrations === true">
                Loading Aweber data...
              </template>
              <template v-else-if="oauthData.authorized === true">
                <div>
                  <div class="mb-2">
                    Your authorized Aweber account:&nbsp;
                    <span class="font-weight-bold">{{
                      oauthData.accountName
                    }}</span>
                  </div>

                  <div>
                    <button
                      type="button"
                      class="btn round-button w-100"
                      @click="handleUnauthorize('aweber')"
                    >
                      Unauthorize this account
                    </button>
                  </div>
                </div>
              </template>
              <template v-else>
                <button
                  type="button"
                  class="btn round-button mt-4 w-100"
                  @click="oAuthLogin('aweber')"
                  v-if="isLoadingIntegrations === false"
                >
                  Click to Authorize Your Aweber Account
                </button>
              </template>
            </div>
            <div
              class="integrate-form-wrapper"
              v-if="selected === 'getresponse'"
            >
              <div class="integrate-form">
                <label>Getresponse Api Key</label>
                <input type="url" v-model="key" />
                <button
                  type="button"
                  class="btn round-button mt-4 w-100"
                  @click="saveKey('getresponse')"
                >
                  Save Key
                </button>
              </div>
            </div>
            <div class="integrate-form-wrapper" v-if="selected === 'mailchimp'">
              <div class="integrate-form">
                <label>MailChimp Api Key</label>
                <input type="url" v-model="key" />

                <label class="mt-3">MailChimp Datacenter</label>
                <input type="datacenter" v-model="mailchimpDatacenter" />

                <button
                  type="button"
                  class="btn round-button mt-4 w-100"
                  @click="saveKey('mailchimp')"
                >
                  Save
                </button>
              </div>
            </div>
            <div class="integrate-form-wrapper" v-if="selected === 'zapier'">
              <div class="integrate-form">
                <label>Zapier Webhook URL</label>
                <input type="url" v-model="key" />
                <button
                  type="button"
                  class="btn round-button mt-4 w-100"
                  @click="saveKey('zapier')"
                >
                  Save
                </button>
              </div>
            </div>
            <div class="integrate-form-wrapper" v-if="selected === 'pabbly'">
              <div class="integrate-form">
                <label>Pabbly Webhook URL</label>
                <input type="url" v-model="key" />
                <button
                  type="button"
                  class="btn round-button mt-4 w-100"
                  @click="saveKey('pabbly')"
                >
                  Save
                </button>
              </div>
            </div>
            <div
              class="integrate-form-wrapper"
              v-if="selected === 'sendinblue'"
            >
              <div class="integrate-form">
                <label>SendInBlue Api Key</label>
                <input type="url" v-model="key" />
                <button
                  type="button"
                  class="btn round-button mt-4 w-100"
                  @click="saveKey('sendinblue')"
                >
                  Save Key
                </button>
              </div>
            </div>
            <div class="integration-form-wrapper" v-if="selected === 'csv'">
              <button
                type="button"
                class="btn round-button"
                @click="downloadCSV()"
              >
                Download CSV File
              </button>
            </div>
          </div>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<style lang="scss" scoped>
#page-integration {
  padding: 50px;
  background-color: white;
  border-radius: 30px;
  height: 100%;
  display: flex;
  flex-direction: column;
  .type-wrapper {
    margin-top: 20px;
    .btn-integrate {
      background-color: #f0f1f3 !important;
      color: black !important;
      padding: 7px 15px !important;
      border-radius: 30px !important;
      font-weight: 700;
      &.active {
        background-color: #1998e4 !important;
        color: white !important;
      }
      &:hover {
        background-color: #51ace0 !important;
        color: white !important;
      }
      img {
        height: 15px;
        margin-right: 7px;
      }
    }
  }
  .integrate-body {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    .integrate-form-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 30px;
    }
    .integrate-form {
      padding: 50px;
      display: flex;
      flex-direction: column;
      border: 1px solid #f0f1f3;
      border-radius: 30px;
      width: 420px;
      label {
        font-weight: 700;
      }
      input {
        padding: 5px 20px;
        border: 1px solid #ccc;
        border-radius: 30px;
        &:focus-visible {
          outline: none !important;
        }
      }
    }
    .csv-download-wrapper {
      text-align: center;
      .btn-success {
        padding: 7px 30px;
      }
    }
  }
  .video-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    width: 100%;
    iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }
  }
}
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';

const autoresponders = [
  'aweber',
  'getresponse',
  'mailchimp',
  'zapier',
  'sendinblue',
  'constantcontact',
  'pabbly',
  'csv'
];

export default {
  name: 'integrations',
  computed: {
    ...mapGetters(['currentUser'])
  },
  data() {
    return {
      isLoadingIntegrations: true,
      integrations: [],
      integrattion: {},
      selected: 'csv',
      key: '',
      mailchimpDatacenter: '',
      oauthData: {
        authorized: false,
        accountName: ''
      },
      videos: [],
      url: ''
    };
  },
  watch: {
    selected: function() {
      this.setAutoresponderData();
    }
  },
  mounted() {
    this.setAutoresponderFromHash();

    const loader = this.$loading.show();

    axios
      .get('/api/integrations')
      .then(res => {
        this.isLoadingIntegrations = false;

        if (res && res.status === 200) {
          this.integrations = res.data;

          this.setAutoresponderData();
        }
      })
      .catch(() => {
        this.isLoadingIntegrations = false;

        this.$func.showTextMessage(
          'Error',
          'Cannot load integrations data.',
          'error'
        );
      })
      .finally(() => {
        loader && loader.hide();
      });

    axios
      .get('/api/integration-videos')
      .then(res => {
        if (res && res.status === 200) {
          this.videos = res.data;
          this.handleSelect('csv');
        } else {
          this.$func.showTextMessage(
            'Error',
            'Cannot load integration videos now.',
            'error'
          );
        }
      })
      .catch(() => {
        this.$func.showTextMessage(
          'Error',
          'Cannot load integration videos now.',
          'error'
        );
      })
      .finally(() => {
        loader && loader.hide();
      });
  },
  methods: {
    getMethod() {
      return 'Token';
    },
    downloadCSV() {
      const loader = this.$loading.show();
      axios
        .get('/api/csv-download/')
        .then(res => {
          if (res && res.status === 200) {
            //
            this.$http
              .get(`${process.env.MIX_APP_URL}/storage/${res.data.path}`, {
                responseType: 'arraybuffer'
              })
              .then(res => {
                let blob = new Blob([res.data]);
                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'Vidpopup.csv';
                link.click();
              });
          } else {
            this.$func.showTextMessage(
              'Error',
              'Something went wrong while downloading',
              'error'
            );
          }
        })
        .catch(err => {
          this.$func.showTextMessage(
            'Error',
            'Something went wrong while downloading',
            'error'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    handleSelect(type) {
      this.selected = type;
      const video = this.videos.find(v => v.type === type);
      if (video) {
        this.url = video.url;
      } else {
        this.url = '';
      }
    },
    handleUnauthorize(autoresponder) {
      const loader = this.$loading.show();

      axios
        .post('/api/integration/unauthorize', {
          autoresponder
        })
        .then(res => {
          if (res && res.status === 400) {
            this.$func.showTextMessage(
              'Error',
              res.data?.error ?? 'Error message not specified!',
              'error'
            );
          }

          if (res && res.status === 200) {
            this.oauthData = {
              authorized: false,
              accountName: ''
            };

            this.$func.makeToast(
              this,
              'Notice',
              `Your ${autoresponder} account is unauthorized.`,
              'success'
            );
          }
        })
        .catch(err => {
          this.$func.showTextMessage(
            'Error',
            'There was an error unauthorizing this account.',
            'error'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    oAuthLogin(type) {
      window.location.replace(`/integrations/oauth/${type}`);
    },
    setAutoresponderData() {
      if (this.isLoadingIntegrations) {
        return;
      }

      const data = this.getUserData(this.selected);

      if (this.selected === 'aweber') {
        if (data) {
          this.oauthData = {
            authorized: true,
            accountName: data?.name ?? 'n/a'
          };
        }

        return;
      }

      if (this.selected === 'mailchimp') {
        this.key = data.apiKey;
        this.mailchimpDatacenter = data.datacenter;

        return;
      }

      this.key = data;
    },
    saveKey(type) {
      if (this.key === '') {
        this.$func.showTextMessage(
          'Notice',
          'Please input your api-key first!',
          'danger'
        );
        return;
      }

      if (type === 'mailchimp' && this.mailchimpDatacenter === '') {
        this.$func.showTextMessage(
          'Notice',
          'Please enter Mailchimp datacenter.',
          'danger'
        );
        return;
      }

      axios
        .post(`/api/integration`, {
          type,
          mailchimp_datacenter: this.mailchimpDatacenter,
          api: this.key
        })
        .then(res => {
          if (res && res.status === 200) {
            this.$func.makeToast(
              this,
              'Notice',
              `${type} api key is saved!`,
              'success'
            );
          }
        })
        .catch(() => {
          this.$func.showTextMessage(
            'Error',
            `Cannot save ${type} key now!Try later please.`,
            'error'
          );
        });
    },
    getUserData(type) {
      const data = this.integrations.find(info => info.type === type);

      if (data) {
        if (type === 'aweber') {
          return data.oauth_data;
        }

        if (type === 'mailchimp') {
          return {
            apiKey: data.api,
            datacenter: data.mailchimp_datacenter
          };
        }

        return data.api;
      }

      return type === 'aweber' || type === 'mailchimp' ? null : '';
    },
    isDisabled(type) {
      const level = this.currentUser.origin_level
        ? this.currentUser.origin_level
        : this.currentUser.level;
      if (['TIER1', 'TIER2', 'TIER3'].includes(level)) {
        return false;
      }
      if (level === 'FET') {
        return true;
      }
      if (level === 'OTO2') {
        return false;
      }
      if (
        ['aweber', 'getresponse', 'constantcontact', 'pabbly', 'csv'].includes(
          type
        )
      ) {
        return false;
      }
      return true;
    },
    setAutoresponderFromHash() {
      const hash = location.hash.substr(1, location.hash.length);

      if (autoresponders.includes(hash)) {
        this.selected = hash;
      }
    }
  }
};
</script>
