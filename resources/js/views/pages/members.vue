<template>
  <div id="page-users">
    <div v-if="['OTO2', 'TIER2', 'TIER3'].includes(currentUser.level)" class="member-form">
      <div class="member-header">
        <h5>
          Manage Your Team ({{ users.length }} / {{ currentUser.whitelabels }})
        </h5>
        <button type="button" class="btn plus-button" @click="addUser">
          +
        </button>
      </div>
      <div class="member-list">
        <div
          class="member-item"
          v-for="(item, idx) in users"
          :key="`user-list-${idx}`"
        >
          <h6>{{ item.name }}<br />{{ item.email }}</h6>
          <div class="item-tool">
            <fa-icon
              :icon="['fas', 'edit']"
              class="text-info btn-tool mr-2"
              @click="editUser(item)"
            />
            <fa-icon
              :icon="['fas', 'trash']"
              class="text-danger btn-tool"
              @click="deleteUser(item)"
            />
          </div>
        </div>
      </div>
    </div>
    <div v-else class="member-form text-center">
      <h5 class="text-center">
        You can't create sub-user, upgrade your plan to use this feature!
      </h5>
      <button type="button" class="btn btn-primary mt-4" @click="upgradePlan">
        Upgrade plan
      </button>
    </div>
    <b-modal
      id="user-modal"
      ref="userModal"
      :title="mode === 'add' ? 'Add new user' : 'Edit user'"
      centered
      hide-footer
    >
      <b-form @submit="handleUser" @reset="closeModal">
        <b-form-group label="User name: " label-for="user-name">
          <b-form-input
            id="user-name"
            v-model="selectedUser.name"
            type="text"
            required
            placeholder="Input your sub-user name here"
          ></b-form-input>
        </b-form-group>
        <b-form-group label="Email address: " label-for="user-email">
          <b-form-input
            id="user-email"
            v-model="selectedUser.email"
            type="email"
            required
            placeholder="Input your sub-user email here"
          ></b-form-input>
        </b-form-group>
        <div class="d-flex justify-content-end">
          <b-button type="submit" variant="primary" class="mr-2">
            {{ mode === 'add' ? 'Add' : 'Update' }}
          </b-button>
          <b-button type="reset" variant="secondary" class="mr-1"
            >Cancel</b-button
          >
        </div>
      </b-form>
    </b-modal>
  </div>
</template>

<style lang="scss" scoped>
$base-color: #3490dc;
#page-users {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  .member-list {
    .member-item {
      margin-top: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #444;
      padding: 5px 20px;
      .btn-tool {
        cursor: pointer;
        &:hover {
          opacity: 0.75;
        }
      }
    }
  }
  .member-form {
    padding: 50px;
    background: white;
    width: 420px;
    border-radius: 30px;
    margin-bottom: 50px;
    max-height: 600px;
    overflow-y: auto;
    .member-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      h5 {
        font-weight: 700;
        margin-bottom: 0px;
      }
      .plus-button {
        background-color: #1998e4;
        color: white;
        font-size: 1.2rem;
        border-radius: 50%;
        padding: 0px;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        &:hover {
          filter: brightness(110%);
        }
      }
    }
  }
}
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
import Swal from 'sweetalert2';
export default {
  name: 'users',
  computed: {
    ...mapGetters(['currentUser'])
  },
  data() {
    return {
      users: [],
      mode: '',
      selectedUser: {
        name: '',
        email: '',
        id: ''
      }
    };
  },
  mounted() {
    const loader = this.$loading.show();
    this.users = [];
    axios
      .get('/api/subusers')
      .then(res => {
        if (res && res.status === 200) {
          res.data.forEach(element => {
            this.users.push({
              id: element.id,
              name: element.name,
              email: element.email
            });
          });
        } else {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot load sub-usres!',
            'danger'
          );
        }
      })
      .catch(() => {
        this.$func.makeToast(this, 'Error', 'Cannot load sub-usres!', 'danger');
      })
      .finally(() => {
        loader && loader.hide();
      });
  },
  methods: {
    addUser() {
      this.mode = 'add';
      this.selectedUser = {
        id: '',
        name: '',
        email: ''
      };
      this.$refs.userModal.show();
    },
    editUser(row) {
      this.mode = 'edit';
      const idx = this.users.findIndex(u => u.id === row.id);
      if (idx > -1) {
        this.selectedUser = {
          id: this.users[idx].id,
          name: this.users[idx].name,
          email: this.users[idx].email
        };
        this.$refs.userModal.show();
      }
    },
    async deleteUser(row) {
      Swal.fire({
        icon: 'warning',
        html: '<h3>Do you want to remove the user?</h3>',
        showCancelButton: true,
        confirmButtonText: 'Remove'
      }).then(async result => {
        if (result.isConfirmed) {
          const loader = this.$loading.show();
          const res = await axios.delete(`/api/delete-user/${row.id}`);
          if (res && res.status === 204) {
            this.$func.showTextMessage('Notice', 'User removed!', 'success');
            const idx = this.users.findIndex(u => u.id === row.id);
            this.users.splice(idx, 1);
          } else {
            this.$func.showTextMessage('Error', res.data.error, 'error');
          }
          loader && loader.hide();
        }
      });
    },
    async handleUser(evt) {
      evt.preventDefault();
      this.$refs.userModal.hide();
      const loader = this.$loading.show();
      const res = await axios.post(`/api/update-user`, {
        id: this.selectedUser.id,
        name: this.selectedUser.name,
        email: this.selectedUser.email
      });
      loader && loader.hide();
      if (res.status === 200) {
        if (this.selectedUser.id === '') {
          Swal.fire({
            icon: 'success',
            html: `<h3>New User Created</h3><h3>Password: ${res.data.pwd}</h3>`,
            showDenyButton: true,
            denyButtonText: 'Copy'
          }).then(async result => {
            if (result.isDenied) {
              this.handleCopy(res.data.pwd);
              this.$func.makeToast(
                this,
                'Notice',
                'Password is copied to clipboard',
                'info'
              );
            }
          });
          this.users.unshift({
            id: res.data.user.id,
            name: res.data.user.name,
            email: res.data.user.email
          });
        } else {
          this.$func.showTextMessage('Notice', 'User info updated!', 'info');
          const idx = this.users.findIndex(u => u.id === this.selectedUser.id);
          if (idx > -1) {
            this.users[idx].name = this.selectedUser.name;
            this.users[idx].email = this.selectedUser.email;
          }
        }
      } else {
        this.$func.showTextMessage('Error', res.data.error, 'error');
      }
    },
    closeModal(evt) {
      evt.preventDefault();
      this.$refs.userModal.hide();
    },
    handleCopy(text) {
      const el = document.createElement('textarea');
      el.value = text;
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
    },
    upgradePlan() {
      this.$router.push({
        path: '/app/memberships'
      });
    }
  }
};
</script>
