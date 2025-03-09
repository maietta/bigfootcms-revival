<script lang="ts">
import { type Writable, writable } from 'svelte/store';
import btOpen from './assets/images/bt_open.png';
import btClose from './assets/images/bt_close.png';

const isEditing: Writable<boolean> = writable(false);
const isPanelVisible: Writable<boolean> = writable(true);
const isLoading: Writable<boolean> = writable(false);
const currentPath: Writable<string> = writable(window.location.pathname);
const currentTab: Writable<string> = writable('content');
const currentSubTab: Writable<string> = writable('basics');

const tabs = [
  { id: 'content', label: 'Content', title: 'Manage website content' },
  { id: 'media', label: 'Media', title: 'Manage media files' },
  { id: 'templates', label: 'Templates', title: 'Manage page templates' },
  { id: 'users', label: 'Users', title: 'Manage users and permissions' },
  { id: 'system', label: 'System', title: 'System settings and configuration' }
];

const contentSubTabs = [
  { id: 'basics', label: 'Basic/SEO Information', title: 'Give this resource a title, keywords and description' },
  { id: 'extended', label: 'Distribution Controls', title: 'Control who, what, when where and why resource becomes available' },
  { id: 'navigation', label: 'Navigation', title: 'Specify the navigational targets' }
];

// Mock function to simulate loading
const simulateLoading = () => {
  isLoading.set(true);
  setTimeout(() => isLoading.set(false), 1000);
};
</script>

<!-- Toggle Button (Always Visible) -->
<button 
  type="button"
  class="fixed top-0 left-2.5 w-8 h-8 cursor-pointer p-1.5 border-none bg-transparent flex items-center z-[2000000001]"
  onclick={() => isPanelVisible.set(!$isPanelVisible)}
  onkeydown={e => e.key === 'Enter' && isPanelVisible.set(!$isPanelVisible)}
>
  <img src={$isPanelVisible ? btClose : btOpen} alt="Toggle panel" class="w-5 h-5" />
</button>

<!-- Status Bar -->
<div class="status-bar fixed -top-6 left-0 right-0 h-6 border border-[#7F8567] border-t-0 z-[2000000000] transition-[top] duration-300 font-['system-ui,_sans-serif']" class:visible={$isPanelVisible} class:loading={$isLoading}>
  <div class="flex items-center h-full px-8">
    <span class="text-[#08215A] text-sm font-semibold">BigfootCMS</span>
  </div>
</div>

<!-- Main Admin Panel -->
<main class="cms-admin fixed -top-full left-0 right-0 min-h-[12.5rem] max-h-[80vh] overflow-y-auto z-[1999999999] transition-[top] duration-300 font-['system-ui,_sans-serif'] text-base" class:visible={$isPanelVisible}>
  <!-- Main Tabs -->
  <nav class="main-tabs relative pt-6 px-4 min-h-9 border-b border-[#7F8567]">
    <ul class="flex relative bottom-0 m-0 p-0 list-none">
      {#each tabs as tab}
        <li class="relative mr-1" class:current={$currentTab === tab.id}>
          <button 
            onclick={() => {
              simulateLoading();
              currentTab.set(tab.id);
            }}
            title={tab.title}
            class="relative px-6 py-2.5 h-10 text-base text-[#08215A] border border-[#7F8567] rounded-t cursor-pointer bg-white/80 hover:bg-white/90"
          >
            {tab.label}
          </button>
        </li>
      {/each}
    </ul>
  </nav>

  <!-- Sub Tabs (for Content section) -->
  {#if $currentTab === 'content'}
    <nav class="sub-tabs relative pt-4 px-4 min-h-8 border-b border-[#7F8567]">
      <ul class="flex relative bottom-0 m-0 p-0 list-none">
        {#each contentSubTabs as tab}
          <li class="relative mr-0.5" class:current={$currentSubTab === tab.id}>
            <button 
              onclick={() => {
                simulateLoading();
                currentSubTab.set(tab.id);
              }}
              title={tab.title}
              class="relative px-4 py-2 h-9 text-base text-[#08215A] border border-[#7F8567] rounded-t cursor-pointer bg-white/80 hover:bg-white/90"
            >
              {tab.label}
            </button>
          </li>
        {/each}
      </ul>
    </nav>
  {/if}

  <!-- Content Area -->
  <div class="flex-1 p-4">
    <div class="panel border border-[#7F8567] rounded p-4">
      {#if $currentTab === 'content'}
        {#if $currentSubTab === 'basics'}
          <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold mb-6">Basic Information</h2>
            <div class="mb-6">
              <label class="block mb-2 font-bold text-[#08215A]">Page Title</label>
              <input type="text" placeholder="Enter page title" class="w-full p-3 border border-[#7F8567] rounded" />
            </div>
            <div class="mb-6">
              <label class="block mb-2 font-bold text-[#08215A]">Meta Description</label>
              <textarea placeholder="Enter meta description" class="w-full p-3 border border-[#7F8567] rounded h-24 resize-y"></textarea>
            </div>
          </div>
        {/if}
      {:else if $currentTab === 'media'}
        <div class="max-w-4xl mx-auto">
          <h2 class="text-2xl font-bold mb-6">Media Library</h2>
          <div class="grid gap-6">
            <div class="border-2 border-dashed border-[#7F8567] p-8 text-center rounded">
              <button class="px-6 py-3 bg-[#08215A] text-white rounded font-semibold hover:bg-[#0A2B75] transition-colors">Upload Media</button>
              <p class="mt-3">or drag and drop files here</p>
            </div>
            <div class="grid grid-cols-[repeat(auto-fill,minmax(10rem,1fr))] gap-4">
              {#each Array(6) as _, i}
                <div class="border border-[#7F8567] rounded overflow-hidden">
                  <div class="aspect-square flex items-center justify-center">Image {i + 1}</div>
                  <div class="p-3 text-base text-[#08215A]">image{i + 1}.jpg</div>
                </div>
              {/each}
            </div>
          </div>
        </div>
      {:else if $currentTab === 'templates'}
        <div class="max-w-4xl mx-auto">
          <h2 class="text-2xl font-bold mb-6">Page Templates</h2>
          <div class="grid gap-6">
            <div class="border border-[#7F8567] rounded p-6">
              <h3 class="text-xl font-bold mb-3">Default Page</h3>
              <p class="mb-6">Standard page layout with header, content, and footer</p>
              <button class="px-6 py-3 bg-[#08215A] text-white rounded font-semibold hover:bg-[#0A2B75] transition-colors">Edit Template</button>
            </div>
            <div class="border border-[#7F8567] rounded p-6">
              <h3 class="text-xl font-bold mb-3">Blog Post</h3>
              <p class="mb-6">Article layout with featured image and sidebar</p>
              <button class="px-6 py-3 bg-[#08215A] text-white rounded font-semibold hover:bg-[#0A2B75] transition-colors">Edit Template</button>
            </div>
          </div>
        </div>
      {:else if $currentTab === 'users'}
        <div class="max-w-4xl mx-auto">
          <h2 class="text-2xl font-bold mb-6">User Management</h2>
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr>
                  <th class="p-4 text-left text-white">Username</th>
                  <th class="p-4 text-left text-white">Role</th>
                  <th class="p-4 text-left text-white">Last Active</th>
                  <th class="p-4 text-left text-white">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="p-4 border-b border-[#7F8567] text-[#08215A]">admin</td>
                  <td class="p-4 border-b border-[#7F8567] text-[#08215A]">Administrator</td>
                  <td class="p-4 border-b border-[#7F8567] text-[#08215A]">Now</td>
                  <td class="p-4 border-b border-[#7F8567]">
                    <button class="px-6 py-2 bg-[#08215A] text-white rounded font-semibold hover:bg-[#0A2B75] transition-colors">Edit</button>
                  </td>
                </tr>
                <tr>
                  <td class="p-4 border-b border-[#7F8567] text-[#08215A]">editor</td>
                  <td class="p-4 border-b border-[#7F8567] text-[#08215A]">Editor</td>
                  <td class="p-4 border-b border-[#7F8567] text-[#08215A]">2 hours ago</td>
                  <td class="p-4 border-b border-[#7F8567]">
                    <button class="px-6 py-2 bg-[#08215A] text-white rounded font-semibold hover:bg-[#0A2B75] transition-colors">Edit</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      {:else if $currentTab === 'system'}
        <div class="max-w-4xl mx-auto">
          <h2 class="text-2xl font-bold mb-6">System Settings</h2>
          <div class="grid grid-cols-[repeat(auto-fit,minmax(20rem,1fr))] gap-8">
            <div class="border border-[#7F8567] rounded p-6">
              <h3 class="mt-0 mb-6 text-lg p-3 text-white rounded">General</h3>
              <div class="mb-6">
                <label class="block mb-2 font-bold text-[#08215A]">Site Title</label>
                <input type="text" value="BigfootCMS Site" class="w-full p-3 border border-[#7F8567] rounded" />
              </div>
              <div class="mb-6">
                <label class="block mb-2 font-bold text-[#08215A]">Site URL</label>
                <input type="text" value="http://localhost:8080" class="w-full p-3 border border-[#7F8567] rounded" />
              </div>
            </div>
            <div class="border border-[#7F8567] rounded p-6">
              <h3 class="mt-0 mb-6 text-lg p-3 text-white rounded">Email</h3>
              <div class="mb-6">
                <label class="block mb-2 font-bold text-[#08215A]">SMTP Server</label>
                <input type="text" placeholder="smtp.example.com" class="w-full p-3 border border-[#7F8567] rounded" />
              </div>
              <div class="mb-6">
                <label class="block mb-2 font-bold text-[#08215A]">SMTP Port</label>
                <input type="number" placeholder="587" class="w-full p-3 border border-[#7F8567] rounded" />
              </div>
            </div>
          </div>
        </div>
      {/if}
    </div>
  </div>
</main>

<style>
  /* Keep only the styles that can't be handled by Tailwind */
  .status-bar {
    background: url('./assets/images/status_bar_bg.png') repeat-x;
  }

  .status-bar.loading {
    background: url('./assets/images/status_bar_bg_animated.gif') repeat-x;
  }

  .status-bar.visible {
    top: 0;
  }

  .cms-admin {
    background: url('./assets/images/whitetransparent.png'), rgba(255, 255, 255, 0.8);
  }

  .cms-admin.visible {
    top: 1.5rem;
  }

  .main-tabs {
    background: url('./assets/images/whitetransparent.png'), rgba(255, 255, 255, 0.8);
  }

  .main-tabs button {
    background: url('./assets/images/whitetransparent.png'), rgba(255, 255, 255, 0.8);
  }

  .main-tabs button:hover {
    background: url('./assets/images/whitetransparent.png'), rgba(255, 255, 255, 0.9);
  }

  .main-tabs li.current button {
    background: url('./assets/images/greentransparent.png'), rgba(127, 133, 103, 0.8);
    border-bottom: none;
    color: white;
    font-weight: 600;
    margin-bottom: -1px;
  }

  .main-tabs li.current::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 1px;
    right: 1px;
    height: 1px;
    background: url('./assets/images/greentransparent.png'), rgba(127, 133, 103, 0.8);
    z-index: 2;
  }

  .sub-tabs {
    background: url('./assets/images/whitetransparent.png'), rgba(255, 255, 255, 0.8);
  }

  .sub-tabs button {
    background: url('./assets/images/whitetransparent.png'), rgba(255, 255, 255, 0.8);
  }

  .sub-tabs button:hover {
    background: url('./assets/images/whitetransparent.png'), rgba(255, 255, 255, 0.9);
  }

  .sub-tabs li.current button {
    background: url('./assets/images/bluetransparent.png'), rgba(8, 33, 90, 0.8);
    border-bottom: none;
    color: white;
    font-weight: 600;
    margin-bottom: -1px;
  }

  .sub-tabs li.current::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 1px;
    right: 1px;
    height: 1px;
    background: url('./assets/images/bluetransparent.png'), rgba(8, 33, 90, 0.8);
    z-index: 2;
  }

  .panel {
    background: url('./assets/images/whitetransparent.png'), rgba(255, 255, 255, 0.8);
  }

  th {
    background: url('./assets/images/bluetransparent.png'), rgba(8, 33, 90, 0.8);
  }

  .setting-group h3 {
    background: url('./assets/images/bluetransparent.png'), rgba(8, 33, 90, 0.8);
  }
</style>
