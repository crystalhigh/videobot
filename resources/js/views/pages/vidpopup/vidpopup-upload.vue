<template>
  <div id="page-vidpopup-upload">
    <b-container>
      <div class="creator-form mx-auto">
        <b-row class="flex-column">
          <b-col>
            <div class="upload-wrapper">
              <input
                accept="video/mp4, video/mpeg, video/quicktime, video/webm"
                type="file"
                hidden
                ref="uploadRef"
                @change="uploadFile"
              />
              <div class="area">
                <b-button @click="handleBrowse">Choose a file</b-button>
                <h6 class="text-danger mt-2">
                  *File size should be less than 20M
                </h6>
              </div>
              <div class="back">
                <fa-icon
                  :icon="['fas', 'times']"
                  class="btn-back"
                  @click="handleBack"
                />
              </div>
            </div>
          </b-col>
        </b-row>
      </div>
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
#page-vidpopup-upload {
  height: 100%;
  padding: 30px;
  border-radius: 30px;
  display: flex;
  justify-content: center;
  .upload-wrapper {
    text-align: center;
    display: flex;
    -webkit-box-align: center;
    align-items: center;
    -webkit-box-pack: center;
    justify-content: center;
    cursor: pointer;
    min-height: 220px;
    flex: 1 1 0%;
    position: relative;
    padding: 16px;
    animation: 0.3s ease-in-out 0s 1 normal none running gWpsBT;
    border-style: dashed;
    border-width: 2px;
    border-radius: 16px;
    outline-color: rgba(128, 128, 128, 0.4);
    background: rgba(128, 128, 128, 0.04);
    transition: background 0.3s ease 0s;
    border-color: rgba(128, 128, 128, 0.2);
    position: relative;
    .area {
      display: flex;
      flex-direction: column;
      -webkit-box-align: center;
      align-items: center;
    }
    .back {
      position: absolute;
      top: -25px;
      right: -15px;
      font-size: 1.5rem;
      .btn-back {
        color: #e3342f;
        cursor: pointer;
        &:hover {
          color: #cc5d59;
        }
      }
    }
  }
}
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
export default {
  name: 'vidpopup-upload',
  data() {
    return {
      id: '',
      step_id: '',
      index: -1,
      file: null
    };
  },
  computed: {
    ...mapGetters(['currentUser'])
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
    this.step_id = this.$route.params.step;
    this.index = this.$route.query.index;
  },
  methods: {
    handleBrowse() {
      this.$refs.uploadRef.click();
    },
    uploadFile(e) {
      if (e.target.files.length === 0) return;
      this.file = e.target.files[0];
      if (!this.file) {
        return;
      }
      if (this.file.size > 20971520) {
        this.$func.makeToast(
          this,
          'Error',
          'File should be less than 20M',
          'danger'
        );
        return;
      }
      const loader = this.$loading.show();

      const formData = new FormData();
      formData.append('file', this.file);
      formData.append('vid', this.id);
      formData.append('fileType', 'video');
      formData.append('sid', this.step_id);
      formData.append('index', this.index);
      axios
        .post('/api/create-step', formData)
        .then(res => {
          if (res.status === 201) {
            loader && loader.hide();
            this.$router.push({
              path: `/app/vidgen/${this.id}/${res.data.id}/edit/overlay`
            });
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot create step right now!',
              'danger'
            );
          }
        })
        .catch(err => {
          console.log('create err=>', err);
          this.$func.makeToast(
            this,
            'Error',
            'Cannot create step right now!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    handleBack() {
      this.$router.push({
        path: `/app/vidgen/${this.id}/${this.step_id}/manage?index=${this.index}`
      });
    }
  }
};
</script>
