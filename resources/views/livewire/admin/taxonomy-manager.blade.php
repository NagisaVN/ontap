<div class="p-6 max-w-3xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-xl font-bold text-slate-900">Taxonomy Manager</h1>
      <p class="text-sm text-slate-500 mt-0.5">Manage the Major → Subject → Sub-subject hierarchy.</p>
    </div>
    <button wire:click="createMajor" class="flex items-center gap-2 h-9 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white text-sm font-semibold transition-colors">
      <!-- Icon: Plus -->
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Major
    </button>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
    <!-- Depth 0: Major -->
    @if(isset($tree))
    @foreach($tree as $major)
      <div x-data="{ open: true, addingChild: false, newName: '' }">
        <div class="flex items-center gap-1 py-1.5 rounded-lg hover:bg-slate-50 group transition-colors mb-0.5" style="padding-left: 8px">
          <!-- Toggle -->
          <button @click="open = !open" class="w-5 h-5 flex items-center justify-center rounded text-slate-400 transition-colors hover:bg-slate-200">
            <!-- Icon: ChevronDown/Right -->
            <svg x-show="open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            <svg x-show="!open" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </button>

          <!-- Name -->
          <span class="flex-1 text-sm font-bold text-slate-900">{{ $major['name'] }}</span>

          <!-- Actions -->
          <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="addingChild = true" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="Add Subject">
              <!-- Icon: Plus -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
            <button wire:click="editNode({{ $major['id'] }})" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-slate-200 transition-colors" title="Edit">
              <!-- Icon: Pencil -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
            </button>
            <button wire:click="deleteNode({{ $major['id'] }})" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Delete">
              <!-- Icon: Trash2 -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
            </button>
          </div>
        </div>

        <!-- Add child inline form -->
        <div x-show="addingChild" style="display: none;" class="flex items-center gap-2 py-1.5 pr-2" style="padding-left: 28px">
          <div class="w-5 shrink-0"></div>
          <input x-model="newName" @keydown.enter="$wire.addChild({{ $major['id'] }}, newName).then(() => { addingChild = false; newName = ''; })" @keydown.escape="addingChild = false; newName = '';" placeholder="New Subject name…" class="flex-1 h-7 px-2 border border-indigo-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/30 bg-indigo-50" />
          <button @click="$wire.addChild({{ $major['id'] }}, newName).then(() => { addingChild = false; newName = ''; })" class="w-6 h-6 flex items-center justify-center text-emerald-600 hover:bg-emerald-50 rounded transition-colors">
            <!-- Icon: Check -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </button>
          <button @click="addingChild = false; newName = '';" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:bg-slate-100 rounded transition-colors">
            <!-- Icon: X -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>

        <!-- Depth 1: Subjects -->
        <div x-show="open" x-transition style="display: none;">
          @if(isset($major['children']))
            @foreach($major['children'] as $subject)
              <div x-data="{ open: false, addingChild: false, newName: '' }">
                <div class="flex items-center gap-1 py-1.5 rounded-lg hover:bg-slate-50 group transition-colors" style="padding-left: 28px">
                  <button @click="open = !open" class="w-5 h-5 flex items-center justify-center rounded text-slate-400 transition-colors hover:bg-slate-200">
                    <svg x-show="open" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                  </button>
                  <span class="flex-1 text-sm font-semibold text-slate-800">{{ $subject['name'] }}</span>
                  <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="addingChild = true" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="Add Sub-subject">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <button wire:click="editNode({{ $subject['id'] }})" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-slate-200 transition-colors" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                    </button>
                    <button wire:click="deleteNode({{ $subject['id'] }})" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Delete">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                  </div>
                </div>

                <!-- Add child inline form -->
                <div x-show="addingChild" style="display: none;" class="flex items-center gap-2 py-1.5 pr-2" style="padding-left: 48px">
                  <div class="w-5 shrink-0"></div>
                  <input x-model="newName" @keydown.enter="$wire.addChild({{ $subject['id'] }}, newName).then(() => { addingChild = false; newName = ''; })" @keydown.escape="addingChild = false; newName = '';" placeholder="New Sub-subject name…" class="flex-1 h-7 px-2 border border-indigo-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/30 bg-indigo-50" />
                  <button @click="$wire.addChild({{ $subject['id'] }}, newName).then(() => { addingChild = false; newName = ''; })" class="w-6 h-6 flex items-center justify-center text-emerald-600 hover:bg-emerald-50 rounded transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </button>
                  <button @click="addingChild = false; newName = '';" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:bg-slate-100 rounded transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </button>
                </div>

                <!-- Depth 2: Sub-subjects -->
                <div x-show="open" x-transition style="display: none;">
                  @if(isset($subject['children']))
                    @foreach($subject['children'] as $sub)
                      <div x-data="{ addingChild: false, newName: '' }">
                        <div class="flex items-center gap-1 py-1.5 rounded-lg hover:bg-slate-50 group transition-colors" style="padding-left: 48px">
                          <div class="w-5 h-5 shrink-0"></div> <!-- Empty space since it has no children -->
                          <span class="flex-1 text-sm font-medium text-slate-700">{{ $sub['name'] }}</span>
                          <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="editNode({{ $sub['id'] }})" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-slate-200 transition-colors" title="Edit">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                            </button>
                            <button wire:click="deleteNode({{ $sub['id'] }})" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Delete">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  @endif
                </div>
              </div>
            @endforeach
          @endif
        </div>
      </div>
    @endforeach
    @endif
  </div>

  <!-- Legend -->
  <div class="mt-4 flex items-center gap-4 text-xs text-slate-400">
    <div class="flex items-center gap-1.5"><span class="font-bold text-slate-600">Bold</span> = Major</div>
    <div class="flex items-center gap-1.5"><span class="font-semibold text-slate-500">Semibold</span> = Subject</div>
    <div class="flex items-center gap-1.5"><span class="text-slate-500">Regular</span> = Sub-subject</div>
  </div>
</div>
