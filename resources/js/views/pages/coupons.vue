<template>
  <div id="page-coupons">
    <h2 class="text-center mb-5">Coupon Admin</h2>
    <b-container class="coupon-list">
      <div class="d-flex align-items-center justify-content-end w-100 mb-1">
        <b-button variant="primary" class="mr-1" @click="addCoupon()">
          <fa-icon :icon="['fas', 'plus']" class="mr-1" />Add
        </b-button>
      </div>
      <b-table
        bordered
        hover
        outlined
        sticky-header
        head-variant="light"
        :fields="fields"
        :items="coupons"
      >
        <template #cell(index)="row">
          <span>{{ row.index + 1 }}</span>
        </template>
        <template #cell(user)="row">
          <span>{{ row.item.user_id ? 'Used' : 'Free' }}</span>
        </template>
        <template #cell(actions)="row">
          <fa-icon
            :icon="['far', 'copy']"
            @click="copyCoupon(row)"
            class="mr-3 text-info cursor-pointer"
          ></fa-icon>
          <fa-icon
            :icon="['fas', 'edit']"
            @click="editCoupon(row)"
            class="mr-3 text-success cursor-pointer"
          ></fa-icon>
          <fa-icon
            :icon="['fas', 'trash']"
            @click="deleteCoupon(row)"
            class="text-danger cursor-pointer"
          ></fa-icon>
        </template>
      </b-table>
    </b-container>
    <b-modal
      id="coupon-modal"
      ref="couponModal"
      :title="mode === 'add' ? 'Add new coupon' : 'Edit coupon'"
      hide-footer
    >
      <b-form @submit="handleCoupon" @reset="closeModal">
        <b-form-group label="Coupons:" label-for="coupon">
          <b-form-textarea
            id="coupon"
            v-model="selected.coupon"
            type="text"
            rows="6"
            max-rows="10"
            required
            placeholder="Input your coupons"
            v-if="mode === 'add'"
          />
          <b-form-input
            id="level"
            v-model="selected.coupon"
            type="text"
            required
            v-if="mode === 'edit'"
            placeholder="Input your level"
          />
        </b-form-group>
        <b-form-group label="Levels: " label-for="level">
          <b-form-input
            id="level"
            v-model="selected.level"
            type="text"
            required
            placeholder="Input your level"
          />
        </b-form-group>
        <div class="d-flex align-items-center justify-content-center">
          <b-button type="submit" variant="primary" class="mr-2">
            {{ mode === 'add' ? 'Add' : 'Update' }}
          </b-button>
          <b-button type="reset" variant="secondary" class="mr-1">
            Canel
          </b-button>
        </div>
      </b-form>
    </b-modal>
  </div>
</template>

<style lang="scss" scoped>
#page-coupons {
  .b-table-sticky-header {
    max-height: 500px;
  }
}
</style>

<script>
import { mapGetters } from 'vuex';
import Swal from 'sweetalert2';
import axios from 'axios';
export default {
  name: 'coupons',
  computed: {
    ...mapGetters(['currentUser'])
  },
  data() {
    return {
      fields: [
        { key: 'index', label: '#' },
        'coupon',
        'level',
        { key: 'user', label: 'Status' },
        { key: 'actions', label: 'Actions' }
      ],
      coupons: [],
      selected: {},
      mode: 'add'
    };
  },
  mounted() {
    if (!this.currentUser?.template_admin) {
      this.$router.push({ name: 'statistics' });
    }
    const loader = this.$loading.show();
    axios
      .get('/api/coupons')
      .then(res => {
        if (res && res.status === 200) {
          this.coupons = res.data.coupons;
        } else {
          this.$func.showTextMessage(
            'Error',
            'Cannot load coupons now!',
            'error'
          );
        }
      })
      .catch(() => {
        this.$func.showTextMessage(
          'Error',
          'Cannot load coupons now!',
          'error'
        );
      })
      .finally(() => {
        loader && loader.hide();
      });
  },
  methods: {
    copyCoupon(row) {
      const el = document.createElement('textarea');
      el.value = row.item.coupon;
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
      this.$func.makeToast(
        this,
        'Notice',
        'Coupon is successfully saved!',
        'info'
      );
    },
    addCoupon() {
      this.mode = 'add';
      this.selected = { coupon: '', level: 'OTO1' };
      this.$refs.couponModal.show();
    },
    editCoupon(u) {
      if (u.item.user_id) {
        this.$func.showTextMessage(
          'Error',
          'This coupon is already used and you cannot update it!',
          'error'
        );
        return;
      }
      this.mode = 'edit';
      this.selected = u.item;
      this.$refs.couponModal.show();
    },
    deleteCoupon(row) {
      Swal.fire({
        icon: 'warning',
        html: '<h3>Do you want to remove the coupon?</h3>',
        showCancelButton: true,
        confirmButtonText: 'Remove'
      }).then(result => {
        if (result.isConfirmed) {
          const loader = this.$loading.show();
          axios
            .delete(`/api/coupon/${row.item.id}`)
            .then(res => {
              if (res && res.status === 204) {
                const idx = this.coupons.findIndex(u => u.id === row.item.id);
                this.coupons.splice(idx, 1);
                this.$func.showTextMessage(
                  'Success',
                  'Coupon is successfully removed!',
                  'succes'
                );
              } else {
                this.$func.showTextMessage(
                  'Error',
                  'Cannot delete the coupon right now!',
                  'error'
                );
              }
            })
            .catch(err => {
              this.$func.showTextMessage(
                'Error',
                'Cannot delete the coupon right now!',
                'error'
              );
            })
            .finally(() => {
              loader && loader.hide();
            });
        }
      });
    },
    handleCoupon(e) {
      e.preventDefault();
      if (this.selected.level === '' || this.selected.coupon === '') {
        this.$func.showTextMessage(
          'Error',
          'Please correct the input!',
          'error'
        );
        return;
      }
      this.$refs.couponModal.hide();
      const loader = this.$loading.show();
      if (this.selected.id) {
        axios
          .put(`/api/coupon/${this.selected.id}`, {
            coupon: this.selected.coupon,
            level: this.selected.level
          })
          .then(res => {
            if (res && res.status === 200) {
              const idx = this.coupons.findIndex(
                c => c.id === this.selected.id
              );
              if (idx > -1) {
                this.coupons[idx].coupon = this.selected.coupon;
                this.coupons[idx].level = this.selected.level;
              }
            } else {
              this.$func.showTextMessage(
                'Error',
                'Cannot update coupon now!',
                'error'
              );
            }
          })
          .catch(err => {
            this.$func.showTextMessage(
              'Error',
              'Cannot update coupon now!',
              'error'
            );
          })
          .finally(() => {
            loader && loader.hide();
          });
      } else {
        // create multiple coupons
        axios
          .post('/api/coupons', {
            coupons: this.selected.coupon.split('\n').join(','),
            level: this.selected.level
          })
          .then(res => {
            if (res && res.status === 200) {
              res.data.coupons.forEach(element => {
                this.coupons.push(element);
              });
            }
            this.$func.showTextMessage(
              'Success',
              'New coupons are successfully added!',
              'success'
            );
          })
          .catch(() => {
            this.$func.showTextMessage(
              'Error',
              'Cannot create coupons',
              'error'
            );
          })
          .finally(() => {
            loader && loader.hide();
          });
      }
    },
    closeModal(e) {
      e.preventDefault();
      this.$refs.couponModal.hide();
    }
  }
};
</script>
