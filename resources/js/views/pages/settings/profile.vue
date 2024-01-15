<template>
  <b-row class="setting-profile">
    <b-col xl="3" md="6">
      <h4>Profile</h4>
      <div class="description">
        Enter Your Personal Information
      </div>
      <div class="avatar">
        <img v-if="user && user.avatar" :src="`${awsUrl}${user.avatar}`" />
        <div class="overlay-upload" @click="handleAvatar">
          Change Image
        </div>
      </div>
    </b-col>
    <b-col md="6" xl="6" offset-xl="1" class="profile-details">
      <b-form-group
        id="profile-name"
        label="Full Name"
        label-for="profile-name-input"
      >
        <b-form-input
          id="profile-name-input"
          v-model="user.name"
          type="text"
          required
          class="borderless-text-field"
        ></b-form-input>
      </b-form-group>
      <b-form-group
        id="profile-email"
        label="Email"
        label-for="profile-email-input"
        class="mt-5"
      >
        <b-form-input
          id="profile-email-input"
          v-model="user.email"
          type="email"
          class="borderless-text-field"
          required
        ></b-form-input>
      </b-form-group>
      <b-form-group
        id="profile-country"
        label="Country / Region"
        label-for="profile-country-input"
        class="mt-5"
      >
        <b-form-input
          id="profile-country-input"
          v-model="user.country"
          type="text"
          class="borderless-text-field"
          required
        ></b-form-input>
      </b-form-group>
      <b-form-group
        id="profile-city"
        label="City"
        label-for="profile-city-input"
        class="mt-5"
      >
        <b-form-input
          id="profile-city-input"
          v-model="user.city"
          type="text"
          class="borderless-text-field"
          required
        ></b-form-input>
      </b-form-group>
      <b-row>
        <b-col>
          <b-form-group
            id="profile-state"
            label="State/Province"
            label-for="profile-state-input"
            class="mt-5"
          >
            <b-form-input
              id="profile-state-input"
              v-model="user.state"
              type="text"
              class="borderless-text-field"
              required
            ></b-form-input>
          </b-form-group>
        </b-col>
        <b-col>
          <b-form-group
            id="profile-state"
            label="Zip Code"
            label-for="profile-zipCode-input"
            class="mt-5"
          >
            <b-form-input
              id="profile-zipCode-input"
              v-model="user.zip_code"
              type="text"
              class="borderless-text-field"
              required
            ></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
      <b-form-group
        id="profile-deep-api"
        label="DeepWord Api Key"
        label-for="profile-deep-api-input"
        class="mt-5"
        v-if="currentUser && currentUser.role !== 'publisher'"
      >
        <b-form-input
          id="profile-deep-api-input"
          v-model="user.deep_api"
          type="text"
          class="borderless-text-field"
          required
        ></b-form-input>
      </b-form-group>
      <b-form-group
        id="profile-deep-secret"
        label="DeepWord Secret Key"
        label-for="profile-deep-secret-input"
        class="mt-5"
        v-if="currentUser && currentUser.role !== 'publisher'"
      >
        <b-form-input
          id="profile-deep-secret-input"
          v-model="user.deep_secret"
          type="text"
          class="borderless-text-field"
          required
        ></b-form-input>
      </b-form-group>
      <div class="mt-5 d-flex justify-content-end">
        <button class="btn round-button" @click="saveProfile">Save</button>
      </div>
    </b-col>
    <input
      type="file"
      accept="image/png, image/jpeg"
      hidden
      ref="uploadImageRef"
      @change="uploadImage"
    />
  </b-row>
</template>

<style lang="scss" moduled>
$base-color: #1998e4;
.setting-profile {
  padding: 40px;
  h4 {
    font-weight: 700;
  }
  .description {
    color: #bbb;
    margin-top: 20px;
  }
  .avatar {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background-color: #c4c4c4;
    overflow: hidden;
    margin-top: 50px;
    position: relative;
    img {
      border-radius: 50%;
      width: 100%;
      height: 100%;
    }
    .overlay-upload {
      background-color: #353745;
      color: #ccc;
      position: absolute;
      bottom: 0;
      height: 50px;
      width: 100%;
      padding: 10px;
      text-align: center;
      cursor: pointer;
      opacity: 0.5;
      &:hover {
        opacity: 0.7;
      }
    }
  }
}
</style>

<script>
import { mapGetters } from 'vuex';
import { UPDATE_AUTH } from '@/services/store/auth.module';
export default {
  name: 'setting-profile',
  computed: {
    ...mapGetters(['currentUser'])
  },
  data() {
    return {
      user: {},
      awsUrl: 'https://vidpopup.s3.amazonaws.com/'
    };
  },
  mounted() {
    this.user = this.currentUser;
    this.awsUrl = process.env.MIX_CDN_URL;
  },
  methods: {
    handleAvatar() {
      this.$refs.uploadImageRef.click();
    },
    uploadImage(e) {
      if (e.target.files.length === 0) return;
      if (!e.target.files[0]) return;
      const loader = this.$loading.show();

      const formData = new FormData();
      formData.append('file', e.target.files[0]);
      axios
        .post('/api/upload-avatar', formData)
        .then(res => {
          if (res && res.status === 200) {
            this.user.avatar = res.data;
          } else {
            this.$func.makeToast(this, 'Error', res.data.error, 'danger');
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot upload logo right now!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    saveProfile() {
      if (this.user.name === '') {
        this.$func.showTextMessage(
          'Notice',
          'Please input your full name',
          'info'
        );
        return;
      }
      if (this.user.email === '') {
        this.$func.showTextMessage('Notice', 'Please input your email', 'info');
        return;
      }
      const loader = this.$loading.show();
      axios
        .post(`/api/save-profile`, this.user)
        .then(res => {
          if (res && res.status === 200) {
            this.$store.dispatch(UPDATE_AUTH, res.data);
          } else {
            this.$func.showTextMessage('Error', res.data.error, 'error');
          }
        })
        .catch(() => {
          this.$func.showTextMessage(
            'Error',
            'Cannot update profile right now!',
            'error'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    }
  }
};
</script>
