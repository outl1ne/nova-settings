<template>
  <loading-view :loading="loading" :key="$route.params.id">
    <form v-if="panels" @submit.prevent="update" autocomplete="off" dusk="nova-settings-form">
      <template v-for="(panel, i) in panelsWithFields">
        <template v-if="panel.component === 'detail-tabs' || panel.component === 'form-tabs'">
          <h1 class="text-90 font-normal text-2xl mb-3 nova-heading" :key="i">{{ panel.name }}</h1>
          <form-tabs
            :key="i"
            :resource-name="'nova-settings'"
            :resource-id="$route.params.id || 'general'"
            :errors="validationErrors"
            :field="{ component: 'tabs', fields: panel.fields }"
            :name="panel.name"
            class="mb-3"
          />
        </template>

        <form-panel
          v-else
          :panel="panel"
          :name="panel.name"
          :key="panel.name"
          :fields="panel.fields"
          :resource-name="'nova-settings'"
          :resource-id="$route.params.id || 'general'"
          mode="form"
          class="mb-6"
          :validation-errors="validationErrors"
        />
      </template>
      <!-- Update Button -->
      <div class="flex items-center" v-if="authorizations.authorizedToUpdate">
        <progress-button type="submit" class="ml-auto" :disabled="isUpdating" :processing="isUpdating">
          {{ __('novaSettings.saveButtonText') }}
        </progress-button>
      </div>
    </form>

    <div class="py-3 px-6 border-50" v-else>
      <div class="flex">
        <div class="w-1/4 py-4">
          <h4 class="font-normal text-80">Error</h4>
        </div>
        <div class="w-3/4 py-4">
          <p class="text-90">{{ __('novaSettings.noSettingsFieldsText') }}</p>
        </div>
      </div>
    </div>
  </loading-view>
</template>

<script>
import { Errors } from 'laravel-nova';

export default {
  metaInfo() {
    return {
      title: this.__('novaSettings.navigationItemTitle'),
    };
  },
  data() {
    return {
      loading: false,
      isUpdating: false,
      fields: [],
      panels: [],
      authorizations: [],
      validationErrors: new Errors(),
    };
  },
  async created() {
    this.getFields();
  },
  watch: {
    $route(to, from) {
      if (to.params.id !== from.params.id) this.getFields();
    },
  },
  methods: {
    async getFields() {
      this.loading = true;
      this.fields = [];

      const params = { editing: true, editMode: 'update' };
      if (this.$route.params.id) params.path = this.$route.params.id;

      const {
        data: { fields, panels, authorizations },
      } = await Nova.request()
        .get('/nova-vendor/nova-settings/settings', { params })
        .catch(error => {
          if (error.response.status == 404) {
            this.$router.push({ name: '404' });
            return;
          }
        });
      this.fields = fields;
      this.panels = panels;
      this.authorizations = authorizations;
      this.loading = false;
    },
    async update() {
      try {
        this.isUpdating = true;
        const response = await this.updateRequest();
        if (response && response.data && response.data.reload === true) {
          location.reload();
          return;
        }
        this.$toasted.show(this.__('novaSettings.settingsSuccessToast'), {
          type: 'success',
        });
        // Reset the form by refetching the fields
        await this.getFields();
        this.isUpdating = false;
        this.validationErrors = new Errors();
      } catch (error) {
        console.error(error);
        this.isUpdating = false;
        if (error && error.response && error.response.status == 422) {
          this.validationErrors = new Errors(error.response.data.errors);
          Nova.error(this.__('There was a problem submitting the form.'));
        }
      }
    },
    updateRequest() {
      return Nova.request().post('/nova-vendor/nova-settings/settings', this.formData);
    },
  },
  computed: {
    formData() {
      return _.tap(new FormData(), formData => {
        _(this.fields).each(field => field.fill(formData));
        formData.append('_method', 'POST');
        if (this.$route.params.id) formData.append('path', this.$route.params.id);
      });
    },
    panelsWithFields() {
      return _.map(this.panels, panel => {
        return {
          name: panel.name,
          component: panel.component,
          helpText: panel.helpText,
          fields: _.filter(this.fields, field => field.panel == panel.name),
        };
      });
    },
  },
};
</script>

<style scoped>
.relationship-tabs-panel {
  flex-direction: column;
}
</style>
