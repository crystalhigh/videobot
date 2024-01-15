<template>
  <div id="page-reply">
    <b-container fluid class="h-100">
      <b-row class="h-100">
        <b-col md="3">
          <div class="vidpop-list">
            <div class="page-header">
              <h5>Latest Replies</h5>
              <img src="/images/icons/bell.svg" />
            </div>
            <label class="mt-4 mb-0">Filter By</label>
            <b-form-select
              v-model="searchOption"
              :options="searchOptions"
            ></b-form-select>
            <div class="search-wrapper">
              <b-form-input
                type="search"
                id="reply-search"
                class="form-control mt-2"
                placeholder="Search by title"
                v-debounce:300ms="updateFilter"
              />
              <fa-icon :icon="['fas', 'search']" class="search-icon" />
            </div>

            <div class="vidpop-items-list" v-if="selectedMode === 'vidpop'">
              <vidpop-list-item
                v-for="(item, index) in vidpops"
                :info="item"
                @itemClick="onSelectVidpop(item)"
                :key="`vidpop-item-${index}`"
                :isActive="item.id === selectedResponder.id"
              />
            </div>
            <div
              class="reply-items-list"
              v-if="selectedMode === 'reply' && responders.length > 0"
            >
              <reply-list-item
                v-for="(itm, idx) in filtered"
                :key="`reply-item-${idx}`"
                :name="
                  itm.autoResponder
                    ? `${itm.autoResponder.name} ${itm.autoResponder.name1}`
                    : 'Guest'
                "
                :date="
                  itm.autoResponder
                    ? itm.autoResponder.created_at
                    : itm.replies[0].created_at
                "
                :active="itm.group === selectedResponder.group"
                :thumb="itm.vidpop.thumb"
                :read="!!itm.replies[0].read"
                @onClick="handleReplyClick(itm)"
                @onDelete="handleDelete(itm)"
              />
            </div>
          </div>
        </b-col>
        <b-col md="9">
          <div class="vidpop-respondent">
            <reply-details
              v-if="selectedResponder.group"
              :responder="selectedResponder"
            />
          </div>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<style lang="scss" scoped>
#page-reply {
  padding: 20px;
  height: 100%;
  .vidpop-split-line {
    margin-top: 30px;
    display: flex;
    align-items: center;
    span {
      color: #c4c4c4;
      font-size: 0.8rem;
    }
    hr {
      flex: 1;
      margin-left: 10px;
      border-top: 2px solid #d8ebfb;
    }
  }
  .search-wrapper {
    position: relative;
    #reply-search {
      padding-left: 40px;
    }
    .search-icon {
      position: absolute;
      left: 15px;
      color: #a1a1a1;
      top: 12px;
    }
  }
  .vidpop-respondent {
    background: white;
    border-radius: 30px;
    height: 100%;
    .reply {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      span {
        color: #000;
        font-size: 20px;
        font-weight: 700;
      }
      .btn-reply {
        position: absolute;
        right: 30px;
        bottom: 30px;
        border-radius: 20px;
      }
      .vidpop-name {
        position: absolute;
        left: 30px;
        bottom: 30px;
      }
    }
  }
  .reply-items-list {
    max-height: 70vh;
    overflow-y: auto;
  }
}
</style>

<script>
import { mapGetters } from 'vuex';
import axios from 'axios';
import Swal from 'sweetalert2';
import VidpopListItem from '@/views/components/vidpop-list-item';
import ReplyListItem from '@/views/components/replies/reply-list-item';
import ReplyDetails from '@/views/components/replies/reply-details';
import { UNREAD_UPDATED } from '@/services/store/vidpopup.module';
export default {
  name: 'replies',
  computed: {
    ...mapGetters(['unread', 'currentUser'])
  },
  data() {
    return {
      vidpops: [],
      filtered: [],
      responders: [],
      selectedMode: 'reply',
      selectedResponder: {},
      search: '',
      searchOptions: [
        {
          value: 'name',
          text: 'Responder Name'
        },
        {
          value: 'vidpop',
          text: 'VidGen Title'
        }
      ],
      searchOption: 'name'
    };
  },
  components: {
    VidpopListItem,
    ReplyListItem,
    ReplyDetails
  },
  watch: {
    search: function(newVal, oldVal) {}
  },
  beforeMount() {
    if (!this.currentUser || (this.currentUser && this.currentUser.role != 'advertiser' && this.currentUser.role != 'origin')) {
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
    const loader = this.$loading.show();
    await this.loadReplies();
    loader && loader.hide();
  },
  methods: {
    async loadReplies() {
      try {
        const { data } = await axios.get('/api/replies');
        // sort responders
        const tmp = data.responders;
        tmp.sort(function(a, b) {
          const d1 = a.autoResponder
            ? a.autoResponder.created_at
            : a.replies[0].created_at;
          const d2 = b.autoResponder
            ? b.autoResponder.created_at
            : b.replies[0].created_at;
          const dt1 = new Date(d1);
          const dt2 = new Date(d2);
          if (dt2 < dt1) {
            return -1;
          } else if (dt2 > dt1) {
            return 1;
          }
          return 0;
        });
        this.responders = tmp;
        let count = 0;
        tmp.forEach(v => {
          const ur = v.replies.filter(r => Number(r.read) === 0);
          count += ur.length;
        });
        this.$store.dispatch(UNREAD_UPDATED, count);
      } catch (err) {
        // console.log(err);
        this.responders = [];
      }
      this.filtered = this.responders;
    },
    async loadVidpops() {
      try {
      } catch (err) {}
    },
    onSelectVidpop(vidpop) {
      this.selectedVidpop = vidpop;

      this.vidpops.forEach(element => {
        element.active = false;
      });
      vidpop.active = true;
    },
    async handleReplyClick(info) {
      this.selectedResponder = {};
      // update read
      const updateRead = info.replies
        .map(r => {
          if (!r.read) {
            return axios.get(`/api/read-reply/${r.id}`);
          }
          return null;
        })
        .filter(f => f);
      if (updateRead && updateRead.length > 0) {
        Promise.all(updateRead)
          .then(() => {
            const idx = this.responders.findIndex(itm => itm === info);
            this.responders[idx].replies.forEach(r => {
              r.read = true;
            });
            const idx1 = this.filtered.findIndex(itm => itm === info);
            this.filtered[idx1].replies.forEach(r => {
              r.read = true;
            });
            this.$store.dispatch(
              UNREAD_UPDATED,
              this.unread - updateRead.length
            );
          })
          .catch(() => {});
      }

      setTimeout(() => {
        this.selectedResponder = info;
      }, 100);
    },
    handleDelete(info) {
      Swal.fire({
        title: 'Warning',
        text: 'Current reply will be removed permanently!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
      }).then(result => {
        if (result.isConfirmed) {
          const loader = this.$loading.show();
          try {
            axios
              .delete(`/api/reply-group/${info.group}`)
              .then(res => {
                loader && loader.hide();
                if (res && res.status === 204) {
                  // successfully removed
                  const index = this.responders.findIndex(
                    x => x.group === info.group
                  );
                  if (this.selectedResponder.group === info.group) {
                    this.selectedResponder = {};
                  }
                  if (index > -1) {
                    this.responders.splice(index, 1);
                  }
                  this.$func.makeToast(this, 'Error', res.data.error, 'error');
                } else {
                  this.$func.showTextMessage('Error', res.data.error, 'error');
                }
              })
              .catch(err => {
                loader && loader.hide();
                this.$func.showTextMessage(
                  'Error',
                  'Something went wrong while delete reply!',
                  'error'
                );
              });
          } catch (e) {
            loader && loader.hide();
          }
        }
      });
    },
    updateFilter(val, e) {
      if (this.searchOption === 'name') {
        this.filtered = this.responders.filter(x =>
          (x.autoResponder ? x.autoResponder.name : 'Guest')
            .toUpperCase()
            .includes(val.toUpperCase())
        );
      } else {
        this.filtered = this.responders.filter(x =>
          x.vidpop.name.toUpperCase().includes(val.toUpperCase())
        );
      }
    }
  }
};
</script>
