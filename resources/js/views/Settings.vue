<template>
  <LoadingView :loading="loading" :key="pageId">
    <Head :title="__('novaSettings.navigationItemTitle') + (pageId !== 'general' ? ` (${pageId})` : '')" />

    <form v-if="fields && fields.length" @submit.prevent="update" autocomplete="off" dusk="nova-settings-form">
      <template v-for="panel in panelsWithFields" :key="panel.name">
        <component
          :is="`form-` + panel.component"
          :panel="panel"
          :name="panel.name"
          :fields="panel.fields"
          :resource-name="'nova-settings'"
          :resource-id="pageId"
          mode="form"
          class="mb-6"
          :validation-errors="validationErrors"
          :show-help-text="true"
        />
      </template>
      <!-- Update Button -->
      <div class="flex items-center" v-if="authorizations.authorizedToUpdate">
        <SettingsLoadingButton
          dusk="update-button"
          type="submit"
          class="ml-auto"
          :disabled="isUpdating"
          :processing="isUpdating"
        >
          {{ __('novaSettings.saveButtonText') }}
        </SettingsLoadingButton>
      </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3" v-else>
      <div class="flex flex-col justify-center align-center">
        <div class="w-3/4 py-4 text-center">
          <p class="text-90">{{ __('novaSettings.noSettingsFieldsText') }}</p>
        </div>
      </div>
    </div>
  </LoadingView>
</template>

<script>
import { Errors } from 'laravel-nova';

export default {
  data() {
    return {
      pageId: false,
      loading: false,
      isUpdating: false,
      fields: [],
      panels: [],
      authorizations: [],
      validationErrors: new Errors(),
    };
  },
  async created() {
    this.pageId = this.$page.props.pageId || 'general';

    this.getFields();
  },
  methods: {
    async getFields() {
      this.loading = true;
      this.fields = [];

      const params = { editing: true, editMode: 'update' };
      if (this.pageId) params.path = this.pageId;

      const {
        data: { fields, panels, authorizations },
      } = await Nova.request()
        .get('/nova-vendor/nova-settings/settings', { params })
        .catch(error => {
          if (error.response.status == 404) {
            // this.$router.push({ name: '404' });
            return;
          }
        });
      this.fields = fields;
      this.panels = panels;
      this.authorizations = authorizations;
      this.loading = false;

      // Dispatch event
      const eventName = this.isUpdating ? 'resource-updated' : 'resource-loaded';
      Nova.$emit(eventName, {
        resourceName: 'nova-settings',
      });
    },
    async update() {
      try {
        this.isUpdating = true;
        const response = await this.updateRequest();
        if (response && response.data) {
          if (response.data.reload === true) {
            location.reload();
            return;
          } else if (response.data.redirect && response.data.redirect.length > 0) {
            location.replace(response.data.redirect);
            return;
          }
        }
        Nova.success(this.__('novaSettings.settingsSuccessToast'));

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
      const formData = new FormData();
      this.fields.forEach(field => field.fill(formData));
      formData.append('_method', 'POST');
      if (this.pageId) formData.append('path', this.pageId);
      return formData;
    },
    panelsWithFields() {
      return this.panels.map(panel => {
        return {
          name: panel.name,
          component: panel.component,
          helpText: panel.helpText,
          fields: this.fields.filter(field => field.panel === panel.name),
          showTitle: panel.showTitle,
        };
      });
    },
  },
};
</script>
