<x-app-layout>
    <x-slot name="header">
        Assigned Items
    </x-slot>

    {{-- Page Heading --}}
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-white">Manage Assigned Items</h2>
        <a href="{{ route('assigned-items.create') }}"
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            <span class="material-icons mr-2">add</span> Assign Item
        </a>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-4 text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto bg-gray-800 rounded-lg shadow">
        <table class="min-w-full table-auto text-white">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Room Number</th>
                    <th class="px-4 py-2 text-left">Item Name</th>
                    <th class="px-4 py-2 text-left">Quantity Assigned</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assignedItems as $assignedItem)
                    <tr class="border-t border-gray-700 hover:bg-gray-700">
                        <td class="px-4 py-2">{{ $assignedItem->room->room_number }}</td>
                        <td class="px-4 py-2">{{ $assignedItem->inventoryItem->name }}</td>
                        <td class="px-4 py-2">{{ $assignedItem->quantity_assigned }}</td>
                        <td class="px-4 py-2">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('assigned-items.edit', $assignedItem->id) }}" class="text-yellow-400 hover:text-yellow-600">
                                    <span class="material-icons align-middle text-base">edit</span>
                                </a>
                                <form action="{{ route('assigned-items.destroy', $assignedItem->id) }}" method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Are you sure you want to unassign this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600">
                                        <span class="material-icons align-middle text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-400">No assigned items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (if needed later) --}}
    {{-- <div class="mt-4">
        {{ $assignedItems->links() }}
    </div> --}}
</x-app-layout>
