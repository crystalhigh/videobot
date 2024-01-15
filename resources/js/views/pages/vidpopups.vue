<template>
  <div id="page-vidgen">
    <b-container fluid class="h-100">
      <b-row class="h-100">
        <b-col md="3">
          <div class="d-flex justify-content-end" v-if="currentUser && currentUser.role === 'agent'">
            <h4 class="mb-0">
              <router-link
                to="/app/vidgen/create"
                tag="span"
                class="btn btn-primary create-vidpop-btn"
              >
                <fa-icon :icon="['fas', 'plus']" /> Add
              </router-link>
            </h4>
          </div>
          <hr />
          <div class="search-wrapper">
            <b-form-input
              type="search"
              id="reply-search"
              class="form-control mt-4"
              placeholder="Search by title"
              v-debounce:300ms="updateFilter"
            />
            <fa-icon :icon="['fas', 'search']" class="search-icon" />
          </div>
          <div class="vidpop-list">
            <vidpop-list-item
              :info="item"
              :index="index"
              v-for="(item, index) in filtered"
              :key="`vidpop-item-${index}`"
              @itemClick="handleSelectVidpop"
            />
          </div>
        </b-col>
        <b-col md="9" class="steps-wrapper">
          <div class="vidpop-design-area">
            <div class="selected-vidpop" v-if="selectedVidpop">
              <b-row>
                <b-col md="12" v-if="!loading">
                  <div class="vidpop-design-header">
                    <h4 class="mb-0">
                      {{ selectedVidpop.name }} ({{ selectedVidpop.advertiser.name }} - ${{ selectedVidpop.cost }} / lead)
                    </h4>
                    <div>
                      <!-- <fa-icon
                        :icon="['fas', 'users']"
                        class="mr-3 vidpop-tool-icon text-primary"
                        @click="handleUsers"
                        title="Assign sub-user to this VidGen"
                        v-if="
                          ['TIER2', 'TIER3', 'OTO2'].includes(currentUser ? currentUser.level : null)
                        "
                      /> -->
                      <fa-icon
                        :icon="['far', 'edit']"
                        class="mr-3 vidpop-tool-icon text-primary"
                        @click="editAdvertiserCost"
                        title="Edit advertiser and cost of current VidGen"
                        v-if="currentUser && currentUser.role === 'agent'"
                      />
                      <fa-icon
                        :icon="['far', 'clone']"
                        class="mr-3 vidpop-tool-icon text-primary"
                        @click="handleClone"
                        title="Clone the current VidGen"
                        v-if="currentUser && currentUser.role === 'agent'"
                      />
                      <fa-icon
                        :icon="['fas', 'external-link-alt']"
                        class="mr-3 vidpop-tool-icon text-primary"
                        @click="handleLiveVidpop"
                        title="Show the Current VidGen Live"
                      />
                      <fa-icon
                        :icon="['far', 'eye']"
                        class="mr-3 vidpop-tool-icon text-primary"
                        @click="handlePreviewVidpop"
                        title="Preview the Current VidGen"
                      />
                      <fa-icon
                        :icon="['far', 'hand-spock']"
                        class="mr-3 vidpop-tool-icon text-primary"
                        @click="handleEnd"
                        title="Edit Current VidGen End Page"
                        v-if="currentUser && currentUser.role === 'agent'"
                      />
                      <fa-icon
                        :icon="['fas', 'plus']"
                        class="mr-3 vidpop-tool-icon text-info"
                        @click="handleAddStep(-1)"
                        title="Add new step to the current VidGen"
                        v-if="currentUser && currentUser.role === 'agent'"
                      />
                      <fa-icon
                        :icon="['fas', 'cog']"
                        class="mr-3 vidpop-tool-icon text-secondary"
                        @click="handleSettingVidpop"
                        title="Edit Current VidGen settings"
                        v-if="currentUser && currentUser.role === 'agent'"
                      />
                      <fa-icon
                        :icon="['fas', 'trash']"
                        class="vidpop-tool-icon text-danger"
                        @click="handleDeleteVidpop"
                        title="Delete the current VidGen"
                        v-if="currentUser && currentUser.role === 'agent'"
                      />
                    </div>
                  </div>
                </b-col>
              </b-row>
              <b-row>
                <b-col md="12" v-if="!loading">
                  <div id="diagram-wrapper">
                    <diagram-viewer
                      :role="currentUser.role"
                      :steps="steps"
                      :vidpopId="selectedVidpop.id"
                      @onAddStep="handleAddStep"
                      @onUpdateIndex="handleUpdateIndex"
                    />
                  </div>
                </b-col>
              </b-row>
            </div>
          </div>
        </b-col>
      </b-row>
    </b-container>
    <b-modal
      id="users-modal"
      ref="usersModal"
      title="Assign Users"
      centered
      hide-footer
      no-close-on-backdrop
    >
      <div class="d-flex flex-column p-2">
        <b-form-checkbox
          v-for="(u, idx) in users"
          :key="`sub-user-${idx}`"
          v-model="u.assigned"
          class="mb-2 text-lg"
        >
          {{ `${u.name} ( ${u.email} )` }}
        </b-form-checkbox>
        <h4 v-if="users.length === 0">
          You don't have any sub-users.
        </h4>
      </div>
      <div class="mt-2 d-flex align-items-center justify-content-center">
        <b-button variant="primary" class="mr-3" @click="handleAssignee"
          >Save</b-button
        >
        <b-button variant="secondary" @click="closeModal">Cancel</b-button>
      </div>
    </b-modal>
    <b-modal
      id="edit-advertiser-cost-modal"
      hide-footer
      title="Edit VidGen"
      centered
      ref="advertiserCostModal"
      size="md">
      <h6>Advertiser: </h6>
      <b-form-select v-model="advertiser_id" :options="options" class="form-control mt-2">
      </b-form-select>
      <h6 class="mt-2">Cost per Lead: </h6>
      <b-input
        type="number"
        class="form-control mt-2"
        v-model="cost"
        placeholder="Cost per lead"
      />
      <div class="mt-3 d-flex align-items-center justify-content-end">
        <b-button variant="primary" class="mr-3" @click="handleAdvCost">Update</b-button>
        <b-button variant="secondary" @click="closeAdvCost">Cancel</b-button>
      </div>
    </b-modal>
  </div>
</template>

<style lang="scss" moduled>
$base-color: #3490dc;
$badge-color: #546acc;
#page-vidgen {
  height: 100%;
  .steps-wrapper {
    border: 1px dashed #3490dc;
    padding: 10px;
    border-radius: 7px;
    min-height: 500px;
    height: auto;
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
  .vidpop-list {
    max-height: 80vh;
    overflow-y: auto;
  }
  .create-vidpop-btn {
    border-radius: 10px;
    width: 100%;
  }
  #diagram-wrapper {
    width: 100%;
    padding: 50px 0px;
    overflow-x: auto;
  }

  .vidpop-design-area {
    padding: 20px;
    .vidpop-design-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #444;
      padding-bottom: 5px;
    }
    .vidpop-tool-icon {
      font-size: 18px;
      cursor: pointer;
      &:hover {
        filter: brightness(130%);
      }
    }
  }
}
</style>

<script>
import Swal from 'sweetalert2';
import { mapGetters } from 'vuex';
import VidpopListItem from '@/views/components/vidpop-list-item';
import DiagramViewer from '@/views/components/diagram-viewer';
import { VIDPOP } from '@/services/store/vidpopup.module';
export default {
  name: 'vidpopups',
  components: {
    VidpopListItem,
    DiagramViewer
  },
  computed: {
    ...mapGetters(['vidpop', 'currentUser'])
  },
  data() {
    return {
      items: [],
      filtered: [],
      selectedVidpop: null,
      selectedIndex: -1,
      steps: [],
      nodes: [
        {
          id: 1,
          left: 0,
          top: 0,
          text: 'Start',
          type: 'end',
          size: 'normal'
        }
      ],
      loading: false,
      width: 1000,
      users: [],
      advertisers: [],
      options: [],
      advertiser_id: '',
      cost: 0,
    };
  },
  beforeMount() {
    if (!this.currentUser || (this.currentUser && this.currentUser.role != 'agent' && this.currentUser.role != 'advertiser' && this.currentUser.role != 'origin')) {
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
    this.items = [];
    const [vidpops, advs, subusers] = await Promise.all([
      axios.get('/api/vidpops'),
      axios.get('/api/advertiser-list'),
      axios.get('/api/subusers')
    ])
      .catch(() => {
        this.$func.makeToast(
          this,
          'Error',
          'Cannot load server data!',
          'danger'
        );
      })
      .finally(() => {
        loader && loader.hide();
      });

    if (vidpops && vidpops.status === 200) {
      let index = 0;
      vidpops.data.forEach(element => {
        if (this.vidpop && element.id === this.vidpop.id) {
          this.items.push({
            ...element,
            active: true
          });
          this.selectedVidpop = element;
          this.selectedIndex = index;
        } else {
          this.items.push({
            ...element,
            active: false
          });
        }
        this.filtered = this.items;
        index++;
      });
      if (this.selectedVidpop) {
        this.handleSelectVidpop(this.selectedVidpop, this.selectedIndex, true);
      }
    } else {
      this.$func.makeToast(this, 'Error', 'Cannot load VidGen', 'danger');
    }
    if (subusers && subusers.status === 200) {
      subusers.data.forEach(element => {
        this.users.push({
          id: element.id,
          name: element.name,
          email: element.email,
          assigned: false
        });
      });
    } else {
      this.$func.makeToast(this, 'Error', 'Cannot load sub-usres!', 'danger');
    }
    if (advs && advs.status == 200) {
      this.advertisers = advs.data;
      this.advertisers.map(val => {
        this.options = [...this.options, {
          value: val.id,
          text: val.name
        }]
      });
    }
    this.loading = false;
  },
  methods: {
    handleAdvCost() {
      if (this.cost == 0) {
        this.$func.makeToast(
          this,
          'Warning',
          'Please input the cost',
          'danger'
        );
        return;
      }
      axios.put('/api/vidpop/' + this.selectedVidpop.id, { advertiser_id: this.advertiser_id, cost: this.cost })
      .then(res => {
        this.selectedVidpop.user_id = this.advertiser_id;
        this.selectedVidpop.cost = this.cost;
        this.filtered[this.selectedIndex].cost = this.cost;
        this.filtered[this.selectedIndex].user_id = this.advertiser_id;
        this.$func.makeToast(
          this,
          'Success',
          'Updated successfully!',
          'success'
        );
        this.$refs.advertiserCostModal.hide();
      })
      .catch(err => {
        console.log('err =>', err);
      })
    },
    closeAdvCost() {
      this.$refs.advertiserCostModal.hide();
    },
    editAdvertiserCost() {
      this.cost = this.selectedVidpop.cost;
      this.advertiser_id = this.selectedVidpop.user_id;
      this.$refs.advertiserCostModal.show();
    },
    handleSelectVidpop(item, index, initial = false) {
      if (this.selectedVidpop === item && !initial) {
        return;
      }
      if (!initial) {
        this.items.forEach(element => {
          element.active = false;
        });
        item.active = true;
        this.selectedVidpop = item;
        this.selectedIndex = index;
      }
      this.steps = [];
      let loader;
      if (!initial) {
        loader = this.$loading.show();
      }
      axios
        .get(`/api/steps?vid=${item.id}`)
        .then(res => {
          if (res.status === 200) {
            this.steps = res.data;
            this.$store.dispatch(VIDPOP, this.selectedVidpop);
            // update node
            this.loading = false;
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot load steps for this VidGen!',
              'danger'
            );
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot load steps for this VidGen!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    handleDeleteVidpop() {
      // show message
      Swal.fire({
        title: 'Warning',
        text: 'Current VidGen will be removed permanently!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
      }).then(result => {
        if (result.isConfirmed) {
          const loader = this.$loading.show();
          axios
            .delete(`/api/vidpop/${this.selectedVidpop.id}`)
            .then(res => {
              if (res && res.status === 204) {
                // reset
                this.$func.makeToast(
                  this,
                  'Success',
                  'VidGen is removed successfully!',
                  'success'
                );
                const index = this.items.findIndex(
                  x => x.id === this.selectedVidpop.id
                );
                if (index > -1) {
                  this.items.splice(index, 1);
                }
                this.selectedVidpop = null;
              } else {
                this.$func.makeToast(this, 'Error', res.data.error, 'danger');
              }
            })
            .catch(err => {
              this.$func.makeToast(
                this,
                'Error',
                'Cannot delete the VidGen now',
                'danger'
              );
            })
            .finally(() => {
              loader && loader.hide();
            });
        }
      });
    },
    handleSettingVidpop() {
      this.$router.push({
        path: `/app/vidgen/${this.selectedVidpop.id}/settings/settings`
      });
    },
    handlePreviewVidpop() {
      const routeData = this.$router.resolve({
        path: `/live/${this.selectedVidpop.code}?preview=1`
      });
      window.open(routeData.href, '_blank');
    },
    handleLiveVidpop() {
      const routeData = this.$router.resolve({
        path: `/live/${this.selectedVidpop.code}`
      });
      window.open(routeData.href, '_blank');
    },
    handleAddStep(idx) {
      this.$router.push({
        path: `/app/vidgen/${this.selectedVidpop.id}/newStep/manage?index=${idx}`
      });
    },
    handleEnd() {
      this.$router.push({
        path: `/app/vidgen/${this.selectedVidpop.id}/end-screen`
      });
    },
    handleUpdateIndex(info) {
      const loader = this.$loading.show();
      axios
        .post(`/api/update-step-index`, {
          vid: this.selectedVidpop.id,
          ...info
        })
        .then(res => {
          if (res && res.status === 200) {
            this.steps = res.data.steps;
            this.$func.showTextMessage(
              'Notice',
              "Please don't forget to update the logic of your step",
              'info'
            );
          } else {
            this.$func.makeToast(this, 'Error', res.data.error, 'error');
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot update step index!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    updateFilter(val, e) {
      this.filtered = this.items.filter(x =>
        x.name.toUpperCase().includes(val.toUpperCase())
      );
    },
    handleClone() {
      Swal.fire({
        icon: 'info',
        html: '<h3>Do you want to clone this VidGen?</h3>',
        showCancelButton: true,
        confirmButtonText: 'Clone'
      }).then(async result => {
        if (result.isConfirmed) {
          const loader = this.$loading.show();
          axios
            .get(`/api/clone-vidpop?id=${this.selectedVidpop.id}`)
            .then(res => {
              loader && loader.hide();
              if (res && res.status === 200) {
                this.$router.push({
                  path: `vidgen/${res.data.vidpop.id}/${res.data.first}/edit/overlay`
                });
              } else {
                this.$func.showTextMessage('Error', 'Server error!', 'error');
              }
            })
            .catch(err => {
              this.$func.showTextMessage('Error', 'Server error!', 'error');
              loader && loader.hide();
            });
        }
      });
    },
    handleUsers() {
      // update assignee tables
      const assignees = this.selectedVidpop.assignee
        ? this.selectedVidpop.assignee.split(',')
        : [];
      this.users.forEach(u => {
        if (assignees.includes(u.id)) {
          u.assigned = true;
        } else {
          u.assigned = false;
        }
      });
      this.$refs.usersModal.show();
    },
    closeModal() {
      this.$refs.usersModal.hide();
    },
    handleAssignee() {
      const assignee = this.users.filter(u => u.assigned).map(m => m.id);
      const str = assignee.join(',');
      this.$refs.usersModal.hide();
      if (this.selectedVidpop.assignee !== str) {
        const loader = this.$loading.show();
        // update
        axios
          .post('/api/vidpop-assignee', {
            id: this.selectedVidpop.id,
            assignee: str
          })
          .then(res => {
            if (res && res.status === 200) {
              this.selectedVidpop.assignee = str;
              const index = this.items.findIndex(
                s => s.id === this.selectedVidpop.id
              );
              this.items[index].assignee = str;
              const idx1 = this.filtered.findIndex(
                s => s.id === this.selectedVidpop.id
              );
              this.filtered[idx1].assignee = str;
            } else {
              this.$func.makeToast(
                this,
                'Error',
                'Cannot update the VidGen now',
                'danger'
              );
            }
          })
          .catch(() => {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot update the VidGen now',
              'danger'
            );
          })
          .finally(() => {
            loader && loader.hide();
          });
      }
    }
  }
};
</script>
