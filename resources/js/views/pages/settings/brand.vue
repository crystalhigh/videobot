<template>
  <div class="page-brand">
    <b-row class="setting-branding" v-if="currentUser && checkLevel()">
      <b-col xl="3" md="6">
        <h4>Branding</h4>
        <div class="brand-list">
          <div class="new-brand" @click="handleSelect('new')">
            Add Brand
          </div>
          <div
            class="brand-item"
            v-for="(b, idx) in brands"
            :key="`brand-item-${idx}`"
            :class="b.id === brand.id ? 'active' : ''"
            @click="handleSelect(b)"
          >
            <span class="brand-text">{{ b.name }}</span>
            <fa-icon
              :icon="['fas', 'trash']"
              class="text-danger delete-brand"
              @click="handleDelete(b)"
            />
          </div>
        </div>
      </b-col>
      <b-col md="6" xl="6" offset-xl="1" class="brand-details">
        <div class="avatar-wrapper">
          <div class="avatar">
            <img v-if="logo !== ''" :src="`${awsUrl}${logo}`" />
            <div class="overlay-upload" @click="handleAvatar">
              Change Image
            </div>
          </div>
        </div>
        <b-form-group
          id="branding-name"
          label="Brand Name"
          label-for="branding-name-input"
          class="mt-4"
        >
          <b-form-input
            id="branding-name-input"
            v-model="brand.name"
            type="text"
            required
            class="borderless-text-field"
          ></b-form-input>
        </b-form-group>
        <b-form-group
          id="branding-url"
          label="Website"
          label-for="branding-url-input"
        >
          <b-form-input
            id="branding-url-input"
            v-model="brand.url"
            type="text"
            required
            class="borderless-text-field"
          ></b-form-input>
        </b-form-group>
        <b-row>
          <b-col>
            <div class="color-wrapper">
              <span class="mr-3 mb-0">Text Color</span>
              <input
                type="color"
                class="text-color"
                ref="colorRef"
                v-model="color"
              />
            </div>
          </b-col>
          <b-col>
            <div class="color-wrapper">
              <span class="mr-3 mb-0">Background Color</span>
              <input
                type="color"
                class="text-color"
                ref="bgColorRef"
                v-model="bgColor"
              />
            </div>
          </b-col>
        </b-row>
        <div class="mt-5 d-flex justify-content-center">
          <button class="btn round-button" @click="saveBrand()">Save</button>
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
    <div class="show-upgrade" v-else>
      <h4>Upgrade your plan to have your own branding!</h4>
      <b-button variant="primary" class="mt-3" @click="handleUpgrade"
        >Upgrade</b-button
      >
    </div>
  </div>
</template>

<style lang="scss" moduled>
$base-color: #1998e4;
.page-brand {
  position: relative;
  height: 100%;
  min-height: 500px;
  width: 100%;
  .setting-branding {
    padding: 40px;
    h4 {
      font-weight: 700;
    }
    .brand-list {
      border: 1px solid $base-color;
      height: 100%;
      overflow-y: auto;
      .new-brand {
        padding: 10px;
        background-color: $base-color;
        font-size: 1.2rem;
        cursor: pointer;
        text-align: center;
        color: white;
        &:hover {
          opacity: 0.75;
        }
      }
      .brand-item {
        padding: 10px 20px;
        background-color: #b4cfe7;
        color: black;
        border-bottom: 1px solid white;
        cursor: pointer;
        font-size: 1.1rem;
        position: relative;
        .delete-brand {
          position: absolute;
          top: 15px;
          right: 15px;
          font-size: 0.85rem;
          &:hover {
            opacity: 0.5;
          }
        }
        .brand-text:hover {
          color: white;
        }
        &.active {
          color: white;
        }
      }
    }

    .avatar-wrapper {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
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
    .color-wrapper {
      display: flex;
      align-items: center;
      span {
        color: #ccc;
        font-size: 1rem;
      }
      input[type='color'] {
        -webkit-appearance: none;
        border: none;
        width: 32px;
        height: 32px;
        width: 30px;
        height: 30px;
        border-radius: 7px;
        display: flex;
        cursor: pointer;
      }
      input[type='color']::-webkit-color-swatch-wrapper {
        padding: 0;
      }
    }
  }
  .show-upgrade {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
  }
}
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
import Swal from 'sweetalert2';
export default {
  name: 'setting-brand',
  computed: {
    ...mapGetters(['currentUser'])
  },
  data() {
    return {
      brands: [],
      brand: {
        id: -1,
        name: '',
        url: '',
        logo: ''
      },
      color: '#ffffff',
      bgColor: '#1998e4',
      logo: '',
      awsUrl: 'https://vidpopup.s3.amazonaws.com/'
    };
  },
  mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;

    if (this.checkLevel()) {
      axios
        .get('/api/brands')
        .then(res => {
          if (res && res.status === 200) {
            this.brands = res.data;
          }
        })
        .catch(err => {
          // console.log(err);
        });
    }
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
        .post('/api/upload-logo', formData)
        .then(res => {
          if (res && res.status === 200) {
            this.brand.logo = res.data;
            this.logo = res.data;
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

    saveBrand() {
      if (this.brand.name === '') {
        this.$func.showTextMessage(
          'Notice',
          'Please input your brand name',
          'info'
        );
        return;
      }
      if (this.brand.url === '') {
        this.$func.showTextMessage(
          'Notice',
          'Please input your website url',
          'info'
        );
        return;
      }
      if (this.brand.logo === '') {
        this.$func.showTextMessage(
          'Notice',
          'Please upload your logo!',
          'info'
        );
        return;
      }

      const loader = this.$loading.show();
      axios
        .post(`/api/brand`, {
          ...this.brand,
          color: this.color,
          bg_color: this.bgColor
        })
        .then(res => {
          if (res && res.status === 200) {
            if (this.brand.id === -1) {
              this.brand = res.data;
              this.brands.unshift(this.brand);
            } else {
              const index = this.brands.find(b => b.id === this.brand.id);
              this.brands[index] = res.data;
            }
          } else {
            this.$func.showTextMessage('Error', res.data.error, 'error');
          }
        })
        .catch(() => {
          this.$func.showTextMessage(
            'Error',
            'Cannot save the brand right now!',
            'error'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    handleSelect(b) {
      if (b === 'new') {
        this.brand = {
          id: -1,
          name: '',
          url: '',
          color: '#ffffff',
          bg_color: '#1998e4'
        };
        this.color = '#ffffff';
        this.bgColor = '#1998e4';
        this.logo = '';
      } else {
        this.brand = b;
        this.color = b.color;
        this.bgColor = b.bg_color;
        this.logo = b.logo;
      }
    },
    handleDelete(b) {
      // delete brand
      Swal.fire({
        icon: 'warning',
        html:
          '<h5>Do you want to remove brand?<br />Already assigned vidgen will be reset as default!</h5>',
        showCancelButton: true,
        confirmButtonText: 'Remove'
      }).then(result => {
        if (result.isConfirmed) {
          const loader = this.$loading.show();
          axios
            .delete(`/api/brand/${b.id}`)
            .then(res => {
              if (res && res.status) {
                const idx = this.brands.findIndex(u => u.id === b.id);
                this.$func.showTextMessage(
                  'Success',
                  'Brand is successfully removed!',
                  'success'
                );
                if (this.brand.id === b.id) {
                  this.brand = {
                    id: -1,
                    name: '',
                    url: '',
                    color: '#ffffff',
                    bg_color: '#1998e4'
                  };
                  this.logo = '';
                }
                this.brands.splice(idx, 1);
              } else {
                this.$func.showTextMessage(
                  'Error',
                  'Cannot delete brand right now!',
                  'error'
                );
              }
            })
            .catch(err => {})
            .finally(() => {
              loader && loader.hide();
            });
        }
      });
    },
    handleUpgrade() {
      this.$router.push({
        name: 'memberships'
      });
    },
    checkLevel() {
      const level = this.currentUser.origin_level
        ? this.currentUser.origin_level
        : this.currentUser.level;

      return ['OTO2', 'TIER2', 'TIER3'].includes(level);
    }
  }
};
</script>
