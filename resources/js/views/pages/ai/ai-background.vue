<template>
  <div class="ai-background">
    <div class="space-between">
      <h5>Background</h5>
      <b-form-checkbox
        v-model="isBackground"
        switch
        size="lg"
        class="cursor-pointer"
      ></b-form-checkbox>
    </div>
    <b-tabs class="bg-tabs">
      <b-tab title="Solid" active>
        <div class="bg-solid-list">
          <b-row>
            <b-col v-for="(s, idx) in solids" :key="`bg-solid-${idx}`" md="3">
              <div
                class="bg-solid"
                v-bind:style="{ background: s }"
                :class="userBg === s ? 'active' : ''"
                @click="handleSelect(s, 'solid')"
              >
                <fa-icon
                  :icon="['fas', 'check-circle']"
                  class="checked"
                  v-if="userBg === s"
                />
              </div>
            </b-col>
          </b-row>
        </div>
      </b-tab>
      <b-tab title="Stock" @click="handleSearch">
        <div class="p-5 text-center">
          <h3>Coming Soon</h3>
        </div>
        <!-- <div class="tab-pixabay">
          <input
            type="search"
            v-model="search"
            v-on:keyup.enter="handleSearch"
            placeholder="Search image from pixabay"
            class="form-control"
          />
          <div v-if="stockImages.length === 0 && searching">
            <inner-loader />
          </div>
          <div class="stock-list" v-if="stockImages.length > 0">
            <b-row>
              <b-col
                md="3"
                class="image-card-wrapper"
                v-for="(img, idx) in stockImages"
                :key="`stock-image-${idx}`"
              >
                <div
                  class="image-card"
                  :class="img.largeImageURL === userBg ? 'active' : ''"
                >
                  <img
                    :src="img.previewURL"
                    @click="handleSelect(img.largeImageURL, 'stock')"
                  />
                  <fa-icon
                    :icon="['fas', 'check-circle']"
                    class="checked"
                    v-if="img.largeImageURL === userBg"
                  />
                </div>
              </b-col>
            </b-row>
            <div class="text-center mt-3 w-100" v-if="page < pageLimit">
              <button
                type="button"
                class="btn btn-loadmore"
                @click="searchPixabay()"
                :disabled="searching || page > pageLimit"
              >
                <fa-icon :icon="['fas', 'spinner']" spin v-if="searching" />
                Load More
              </button>
            </div>
          </div>

          <h5
            class="text-center mt-3"
            v-if="stockImages.length === 0 && !searching"
          >
            Not Found!
          </h5>
        </div> -->
      </b-tab>
      <b-tab title="Images" @click="loadUploads">
        <div class="p-5 text-center">
          <h3>Coming Soon</h3>
        </div>
        <!-- <div class="tab-uploads">
          <div class="upload-new-wrapper">
            <span @click="handleUpload">
              <fa-icon :icon="['fas', 'plus']" class="mr-2" />Upload Image
            </span>
            <input
              type="file"
              accept="image/png, image/jpeg"
              hidden
              ref="uploadImageRef"
              @change="uploadImage"
            />
          </div>
          <div class="uploaded-list-wrapper">
            <div class="uploaded-list">
              <div v-if="uploads.length === 0 && searching">
                <inner-loader />
              </div>
              <div v-if="uploads.length > 0">
                <b-row>
                  <b-col
                    md="3"
                    class="image-card-wrapper"
                    v-for="(img, idx) in uploads"
                    :key="`upload-image-${idx}`"
                  >
                    <div
                      class="image-card"
                      :class="
                        `/storage/${img.thumbnail}` === userBg ? 'active' : ''
                      "
                    >
                      <img
                        :src="`/storage/${img.thumbnail}`"
                        @click="
                          handleSelect(`/storage/${img.thumbnail}`, 'upload')
                        "
                      />
                      <fa-icon
                        :icon="['fas', 'check-circle']"
                        class="checked"
                        v-if="`/storage/${img.thumbnail}` === userBg"
                      />
                    </div>
                  </b-col>
                </b-row>
              </div>
              <h5
                class="text-center mt-3"
                v-if="uploads.length === 0 && !searching"
              >
                Not Found!
              </h5>
            </div>
          </div>
        </div> -->
      </b-tab>
    </b-tabs>
    <div class="ai-soundtrack">
      <div class="space-between">
        <h5>Soundtrack</h5>
        <b-form-checkbox
          v-model="isSoundtrack"
          switch
          size="lg"
          class="cursor-pointer"
        ></b-form-checkbox>
      </div>
      <div class="ai-soundtrack-list">
        <b-row>
          <b-col
            class="mb-3"
            md="4"
            v-for="(s, idx) in soundTracks"
            :key="`soundtracks-${idx}`"
          >
            <div
              class="soundtrack-item"
              :class="soundtrack.id === s.id ? 'active' : ''"
              @click="handleSoundtrack(s)"
            >
              <div class="soundtrack-play">
                <fa-icon
                  :icon="[
                    'fas',
                    isPlaying && playSource === s.path ? 'stop' : 'play'
                  ]"
                  class="play-icon"
                  @click="handlePlaysoundtrack($event, s)"
                />
              </div>
              <div class="text-center mt-1 soundtrack-text">{{ s.title }}</div>
              <fa-icon
                :icon="['fas', 'check-circle']"
                class="checked"
                v-if="soundtrack.id === s.id"
              />
            </div>
          </b-col>
        </b-row>
      </div>
    </div>
    <audio class="" id="voice-preview">
      <source
        :src="playSource !== '' ? `/storage/${playSource}` : ''"
        type="audio/mp3"
      />
    </audio>

    <div class="vidpopup-step-button-group mt-5">
      <div>
        <button type="button" class="btn circle-button" @click="handlePrev">
          <fa-icon :icon="['fas', 'arrow-left']" />
        </button>
        <button
          type="button"
          @click="renderVideo"
          class="btn render-video ml-3"
        >
          Generate Video
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" moduled>
$base-color: #1998e4;
.ai-background {
  .bg-tabs {
    margin-top: 30px;
    .nav-tabs {
      .nav-link {
        color: #777;
        font-weight: bold;
        &.active,
        &:hover,
        &:focus {
          color: $base-color;
          border-color: #ffffff #ffffff $base-color #ffffff !important;
        }
        &.active {
          border-width: 2px;
        }
      }
    }
    .tab-content {
      height: 380px;
      padding: 10px;
    }
    .bg-solid {
      width: 100%;
      height: 120px;
      margin-top: 20px;
      border-radius: 10px;
      cursor: pointer;
      position: relative;
      &:hover {
        opacity: 0.8;
      }
      &.active {
        border: 2px solid $base-color;
        .checked {
          color: $base-color;
          position: absolute;
          top: -6px;
          right: -6px;
          z-index: 2;
          font-size: 1rem;
          background-color: white;
          border-radius: 50%;
        }
      }
    }
    .tab-pixabay {
      position: relative;
      .stock-list {
        overflow-x: hidden;
        overflow-y: auto;
        height: 290px;
        margin-top: 10px;
        padding: 0px 10px;
      }
      .btn-loadmore {
        padding: 7px 30px;
        background-color: black;
        color: white;
      }
    }
    .tab-uploads {
      .upload-new-wrapper {
        text-align: right;
        width: 100%;
        padding: 5px;
        span {
          color: $base-color;
          cursor: pointer;
          &:hover {
            opacity: 0.75;
          }
        }
      }
      .uploaded-list-wrapper {
        height: 260px;
        .uploaded-list {
          height: 100%;
          overflow-y: auto;
          overflow-x: hidden;
          padding: 0px 10px;
        }
      }
    }
    .image-card-wrapper {
      margin-top: 5px;
      cursor: pointer;
      padding: 0px 5px;
      display: flex;
      align-items: center;
      justify-content: content;
      .image-card {
        position: relative;
        &.active {
          border: 2px solid $base-color;
          padding: 2px;
          .checked {
            color: $base-color;
            position: absolute;
            top: -6px;
            right: -6px;
            z-index: 2;
            font-size: 1rem;
            background-color: white;
            border-radius: 50%;
          }
        }
      }
    }
  }
  .ai-soundtrack {
    .ai-soundtrack-list {
      max-height: 220px;
      overflow-y: auto;
      padding: 20px;
      .soundtrack-item {
        cursor: pointer;
        position: relative;
        .soundtrack-play {
          width: 100%;
          height: 70px;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #9cb7ec;
          color: white;
          font-size: 1.5rem;
          border-radius: 5px;
          border: 2px solid transparent;
          .play-icon {
            cursor: pointer;
            &:hover {
              opacity: 0.75;
            }
          }
        }
        .soundtrack-text {
          font-size: 0.8rem;
        }
        &.active {
          .soundtrack-play {
            border: 2px solid $base-color;
          }
          .checked {
            color: $base-color;
            position: absolute;
            top: -6px;
            right: -6px;
            z-index: 2;
            font-size: 1rem;
            background-color: white;
            border-radius: 50%;
          }
        }
      }
    }
  }
  .render-video {
    background-color: $base-color;
    padding: 10px 40px;
    color: white;
    border-radius: 30px;
    &:hover {
      opacity: 0.75;
    }
  }
}
#voice-preview {
  display: none;
}
</style>

<script>
import InnerLoader from '@/views/components/inner-loader';
import axios from 'axios';
export default {
  name: 'ai-background',
  components: {
    InnerLoader
  },
  data() {
    return {
      isBackground: true,
      isSoundtrack: true,
      soundTracks: [],
      soundtrack: {},
      solids: [
        '#9CB7EC',
        '#97FFC1',
        '#6AEDFF',
        '#DFB7FF',
        '#51DA67',
        '#4B4B4B',
        '#C7CDCE',
        '#F4E7FF'
      ],
      userBg: '',
      stockImages: [],
      uploads: [],
      search: '',
      searching: false,
      page: 1,
      totalCount: 0,
      pageLimit: 4,
      isPlaying: false,
      playSource: ''
    };
  },
  mounted() {
    // load soundtracks
    axios.get(`/api/soundtracks`).then(res => {
      if (res && res.status === 200) {
        this.soundTracks = res.data.soundtracks;
      } else {
        // show message
      }
    });
  },
  methods: {
    renderVideo() {
      if (this.isPlaying) {
        const voice = document.getElementById('voice-preview');
        voice.load();
        voice.pause();
        voice.currentTime = 0;
      }
      this.$emit('onRender');
    },
    handlePrev() {
      if (this.isPlaying) {
        const voice = document.getElementById('voice-preview');
        voice.load();
        voice.pause();
        voice.currentTime = 0;
      }
      this.$emit('onPrev');
    },
    handleSearch() {
      this.stockImages = [];
      this.page = 1;
      this.searchPixabay();
    },
    searchPixabay() {
      if (this.page > this.pageLimit) return;
      this.searching = true;
      axios
        .get(`/api/pixabay-image?text=${this.search}&page=${this.page}`)
        .then(res => {
          if (res && res.status === 200) {
            this.stockImages = [
              ...this.stockImages,
              ...res.data.images.hits
            ].filter(v => v.imageWidth === 1920 && v.imageHeight === 1080);
            this.totalCount = res.data.images.totalHits;
            this.page += 1;
            if (this.stockImages.length >= this.totalCount) {
              this.page = this.pageLimit + 2;
            }
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot load images from pixabay',
              'danger'
            );
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot load images from pixabay',
            'danger'
          );
        })
        .finally(() => {
          this.searching = false;
        });
    },
    handleSelect(info, type) {
      this.userBg = info;
      this.$emit('onBackground', {
        bg: info,
        type
      });
    },
    loadUploads() {
      this.searching = true;
      axios
        .get('/api/uploaded-image')
        .then(res => {
          if (res && res.status === 200) {
            this.uploads = res.data.images;
          } else {
            this.uploads = [];
          }
        })
        .catch(err => {
          this.uploads = [];
        })
        .finally(() => {
          this.searching = false;
        });
    },
    handleUpload() {
      this.$refs.uploadImageRef.click();
    },
    uploadImage(e) {
      if (e.target.files.length === 0) return;
      if (!e.target.files[0]) return;
      const loader = this.$loading.show();

      const formData = new FormData();
      formData.append('file', e.target.files[0]);
      axios
        .post('/api/upload-image', formData)
        .then(res => {
          if (res && res.status === 201) {
            this.uploads.push(res.data);
          } else {
            this.$func.makeToast(this, 'Error', res.data.error, 'danger');
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot upload image right now!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    handleSoundtrack(itm) {
      this.soundtrack = itm;
    },
    handlePlaysoundtrack(e, itm) {
      e.stopPropagation();
      const voice = document.getElementById('voice-preview');
      voice.load();
      voice.pause();
      voice.currentTime = 0;
      if (itm.path === this.playSource && this.isPlaying) {
        // stop
        this.isPlaying = false;
        return;
      } else {
        this.playSource = itm.path;
        voice.load();
      }
      voice.play();
      this.isPlaying = true;
    }
  }
};
</script>
