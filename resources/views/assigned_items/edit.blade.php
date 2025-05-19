<x-app-layout>
    <x-slot name="header">
        <h2 class="text-white text-xl font-semibold">
            Edit Assigned Item
        </h2>
    </x-slot>

    <div class="bg-gray-800 rounded-lg shadow p-6 text-white max-w-xl mx-auto mt-6">
        @if ($errors->any())
            <div class="mb-4 text-red-400">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('assigned-items.update', $assignedItem->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="room_id" class="block text-sm font-medium">Room</label>
                <select name="room_id" id="room_id" required
                        class="w-full mt-1 bg-gray-700 border border-gray-600 rounded px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" {{ $assignedItem->room_id == $room->id ? 'selected' : '' }}>
                            {{ $room->room_number }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="inventory_item_id" class="block text-sm font-medium">Item</label>
                <select name="inventory_item_id" id="inventory_item_id" required
                        class="w-full mt-1 bg-gray-700 border border-gray-600 rounded px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    @foreach ($inventoryItems as $item)
                        <option value="{{ $item->id }}" {{ $assignedItem->inventory_item_id == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="quantity_assigned" class="block text-sm font-medium">Quantity</label>
                <input type="number" name="quantity_assigned" id="quantity_assigned" min="1" required
                       value="{{ $assignedItem->quantity_assigned }}"
                       class="w-full mt-1 bg-gray-700 border border-gray-600 rounded px-4 py-2 text-white focus:outline-none focus:border-blue-500" />
            </div>

            <div class="flex justify-end">
                <a href="{{ route('assigned-items.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded mr-2">Cancel</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>
    </div>
</x-app-layout>