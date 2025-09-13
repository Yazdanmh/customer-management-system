<template>
  <AdminLayout :setting="props.setting" :user="props.user" :permissions="props.permissions">
    <!-- Stats Cards Row -->
    <div class="row">
      <!-- Customer Stats -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card customer-card h-100">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="card-subtitle mb-1">Total Customers</div>
                <h3 class="card-title mb-0">{{ props.stats.customers.total }}</h3>
              </div>
              <div class="card-icon">
                <i class="bi bi-people-fill"></i>
              </div>
            </div>
            <div class="mt-3">
              <span class="badge bg-light text-dark">
                <i class="bi bi-arrow-up-circle-fill text-success me-1"></i>
                12% from last month
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card customer-card h-100">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="card-subtitle mb-1">Active Customers</div>
                <h3 class="card-title mb-0">{{ props.stats.customers.active }}</h3>
              </div>
              <div class="card-icon">
                <i class="bi bi-person-check-fill"></i>
              </div>
            </div>
            <div class="mt-3">
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" role="progressbar" 
                     :style="'width: ' + (props.stats.customers.active / props.stats.customers.total * 100) + '%;'" 
                     :aria-valuenow="(props.stats.customers.active / props.stats.customers.total * 100)" 
                     aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <small class="text-muted">{{ (props.stats.customers.active / props.stats.customers.total * 100).toFixed(1) }}% of total</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card customer-card h-100">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="card-subtitle mb-1">Inactive Customers</div>
                <h3 class="card-title mb-0">{{ props.stats.customers.inactive }}</h3>
              </div>
              <div class="card-icon">
                <i class="bi bi-person-dash-fill"></i>
              </div>
            </div>
            <div class="mt-3">
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-secondary" role="progressbar" 
                     :style="'width: ' + (props.stats.customers.inactive / props.stats.customers.total * 100) + '%;'" 
                     :aria-valuenow="(props.stats.customers.inactive / props.stats.customers.total * 100)" 
                     aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <small class="text-muted">{{ (props.stats.customers.inactive / props.stats.customers.total * 100).toFixed(1) }}% of total</small>
            </div>
          </div>
        </div>
      </div>

      <!-- User Stats -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card user-card h-100">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="card-subtitle mb-1">Total Users</div>
                <h3 class="card-title mb-0">{{ props.stats.users.total }}</h3>
              </div>
              <div class="card-icon">
                <i class="bi bi-person-badge-fill"></i>
              </div>
            </div>
            <div class="mt-3">
              <span class="badge bg-light text-dark">
                <i class="bi bi-arrow-up-circle-fill text-success me-1"></i>
                8% from last month
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Customers Table -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card table-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span>Recent Customers</span>
            <Link :href="route('customers.index')" class="btn btn-sm btn-outline-primary">View All</Link>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(customer, index) in props.stats.customers.recent" :key="customer.id">
                    <td>{{ index + 1 }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle p-2 me-2">
                          <i class="bi bi-person-circle"></i>
                        </div>
                        <div>{{ customer.name }}</div>
                      </div>
                    </td>
                    <td>{{ customer.username }}</td>
                    <td>
                      <span :class="['badge', customer.status ? 'bg-success' : 'bg-secondary']">
                        <i :class="['me-1', customer.status ? 'bi bi-check-circle-fill' : 'bi bi-dash-circle-fill']"></i>
                        {{ customer.status ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    
                  </tr>
                  <tr v-if="props.stats.customers.recent.length === 0">
                    <td colspan="5" class="text-center text-muted py-4">
                      <i class="bi bi-people display-4 d-block mb-2"></i>
                      No customers found.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Users Table -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card table-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span>Recent Users</span>
            <Link :href="route('users.index')" class="btn btn-sm btn-outline-primary">View All</Link>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(user, index) in props.stats.users.recent" :key="user.id">
                    <td>{{ index + 1 }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle p-2 me-2">
                          <i class="bi bi-person-circle"></i>
                        </div>
                        <div>{{ user.name }}</div>
                      </div>
                    </td>
                    <td>{{ user.email }}</td>
                   
                  </tr>
                  <tr v-if="props.stats.users.recent.length === 0">
                    <td colspan="4" class="text-center text-muted py-4">
                      <i class="bi bi-person display-4 d-block mb-2"></i>
                      No users found.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  setting: Object,
  user: Object,
  permissions: Array,
  stats: Object, // { customers: { total, active, inactive, recent }, users: { total, recent } }
});
</script>

<style scoped>
.stats-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s, box-shadow 0.3s;
}
.stats-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}
.card-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  margin-bottom: 15px;
}
.customer-card .card-icon {
  background-color: rgba(13, 110, 253, 0.15);
  color: #0d6efd;
}
.user-card .card-icon {
  background-color: rgba(111, 66, 193, 0.15);
  color: #6f42c1;
}
.card-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}
.card-subtitle {
  color: #6c757d;
  font-weight: 500;
  font-size: 0.95rem;
}
.table-card {
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: none;
}
.table-card .card-header {
  background-color: #fff;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  font-weight: 600;
  padding: 1.2rem 1.5rem;
  font-size: 1.1rem;
}
.badge {
  padding: 0.5em 0.8em;
  border-radius: 6px;
  font-weight: 500;
}
</style>