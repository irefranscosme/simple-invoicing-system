@extends('layout.base')

@section('content')
    <section class="grid grid-cols-desktop gap-2 pt-5">
        <aside class="overflow-y-auto h-[90vh] pr-2">
            <x-invoice-list :invoices="$invoices" />
        </aside>
        <section>
            <form action={{ route('invoice.update', ['invoice' => isset($invoice->id) ? $invoice->id : 0]) }} method="POST"
                class="border bg-white px-4 py-6" id="invoice-item">
                @method('PUT')
                @csrf
                <input type="hidden" name="customer_id" id="customer-input-id"
                    value="{{ isset($invoice->id) ? $invoice->customer->id : 0 }}">
                <section class="flex justify-between pr-10 mb-10">
                    <section class="flex gap-2">
                        <figure>
                            <img src="{{ asset('assets/icons/irefrans-cosme.png') }}" alt="irefrans cosme logo"
                                class="w-[10em]">
                        </figure>
                        <section>
                            <h1 class="uppercase text-3xl mb-2">Company Name</h1>
                            <article>
                                <p>460-1086, Kitazukamachi</p>
                                <p>+81 +8171-562-7817</p>
                                <p>hamada.mitsuru@tanabe.biz</p>
                            </article>
                        </section>
                    </section>
                    <section>
                        <h1 class="uppercase text-3xl mb-2">Invoice
                            #{{ isset($invoice) ? Str::limit($invoice->invoice_number, '8', '') : '' }}</h1>
                        <article>
                            <p class="text">Date:
                                {{ isset($invoice) ? date('F d, Y', strtotime($invoice->invoice_date)) : '' }}
                            </p>
                        </article>
                        <article class="mt-2 gap-5 relative max-w-fit">
                            <label for="" class="block">Name:
                                <input type="text" id="customer-input"
                                    value="{{ isset($invoice->customer->name) ? $invoice->customer->name : 'N/A' }}"
                                    class="border px-2 rounded pointer-events-none customer-input">
                            </label>
                            <label for="" class="block mt-2">Email:
                                <input type="text" id="customer-input-email"
                                    value="{{ isset($invoice->customer->name) ? $invoice->customer->email : 'N/A' }}"
                                    class="border px-2 rounded pointer-events-none customer-input-email">
                            </label>
                            <menu class="absolute top-0 right-[-2em] mt-0">
                                <li>
                                    <button type="button" id="edit-button">
                                        <figure><img src="{{ asset('assets/icons/icons8-edit-96.png') }}" alt="edit icon"
                                                class="w-6"></figure>
                                    </button>
                                </li>
                            </menu>
                        </article>
                    </section>
                </section>
                {{-- Items --}}
                <fieldset class="mt-2 gap-5">
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right border">
                            <thead class="text-xs uppercase bg-slate-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Product / Service
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Quantity
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Base Price
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table-body" class="[&>*:nth-child(even)]:bg-slate-50">
                                @isset($invoice->items)
                                    @foreach ($invoice->items as $item)
                                        <tr class="bg-white">
                                            <input type="hidden" class="w-full h-full" name="invoice_item[]"
                                                value="{{ $item->id }}">
                                            <td class="p-3">
                                                <input type="text" class="w-full h-full" name="type[]"
                                                    value="{{ $item->type }}">
                                            </td>
                                            <td class="p-3">
                                                <input type="text" class="w-full h-full" name="product_service[]"
                                                    value="{{ $item->product_service }}">
                                            </td>
                                            <td class="p-3">
                                                <input type="number" min="0" class="w-full h-full quantity"
                                                    value={{ $item->quantity }} name="quantity[]">
                                            </td>
                                            <td class="p-3">
                                                <input type="text" class="w-full h-full base-price" name="base_price[]"
                                                    value="{{ $item->base_price }}">
                                            </td>
                                            <td class="p-3">
                                                <input type="text" class="w-full h-full" value="{{ $item->subtotal }}"
                                                    disabled>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endisset
                            </tbody>
                        </table>

                    </div>
                </fieldset>
                <fieldset class="flex justify-between items-start">
                    <menu>
                        <li>
                            <button type="button"
                                class="px-2 border mt-2 border-slate-300 rounded bg-slate-200 hover:border-slate-200  hover:bg-slate- shadow-sm text-sm"
                                id="add-item-btn" title="This button will add a slot on the table for more invoice">
                                <figure>
                                    <img src="{{ asset('assets/icons/icons8-insert-96.png') }}"
                                        alt="insert icon ios created by icons8" class="w-[2em]">
                                </figure>
                            </button>
                        </li>
                    </menu>
                    <table class="w-[20rem] text-sm text-left rtl:text-right mt-10">
                        <tbody class="text-sm uppercase border">
                            <tr class="border-b">
                                <td class="py-1 text-right font-bold">
                                    Total
                                </td>
                                <td class="px-2 text-right">
                                    P{{ $invoice->items->sum('subtotal') }}
                                </td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-1 text-right font-bold">
                                    Discount
                                </td>
                                <td>
                                    <input type="text" class="w-full h-full pl-2 text-right" name="discount"
                                        value="{{ isset($invoice->total) ? intval($invoice->total->discount) : 0 }}% ">
                                </td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-1 text-right font-bold">
                                    Vat
                                </td>
                                <td class="px-2 text-right">
                                    P{{ isset($invoice->total) ? $invoice->total->vat : 0 }}
                                </td>
                            </tr>
                            <tr class="border-b bg-slate-50">
                                <td class="py-1 text-right font-bold">
                                    Grand Price
                                </td>
                                <td class="px-2 text-right">
                                    P{{ isset($invoice->total) ? $invoice->total->grand_price : 0 }}
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </fieldset>
            </form>
            <menu class="flex justify-between">
                <li>
                    <button type="submit" class="border px-4 py-1 rounded mt-2 bg-slate-300 hover:shadow"
                        form="invoice-item">Save</button>
                    <a href="{{ route('dashboard.index') }}">
                        <button type="button" class="border px-4 py-1 rounded mt-2 bg-slate-50 hover:shadow"
                            form="invoice-item">Cancel</button>
                    </a>
                </li>
                <li>
                    <a href="#payment">
                        <button class="border px-3 py-1 rounded mt-2 bg-slate-300 hover:shadow">Create
                            Payment</button>
                    </a>
                </li>
            </menu>
        </section>
    </section>
    <section>
        <x-modal id="modal">
            <h2 class="text-lg font-bold">Customer</h2>
            <label for="" class="block">Name:
                <input type="text" id="customer-input"
                    value="{{ isset($invoice->customer->name) ? $invoice->customer->name : 'N/A' }}"
                    class="border px-2 py-1 rounded pointer-events-none customer-input">
            </label>
            <label for="" class="block">Email:
                <input type="text" id="customer-input-email"
                    value="{{ isset($invoice->customer->name) ? $invoice->customer->email : 'N/A' }}"
                    class="border px-2 py-1 rounded pointer-events-none customer-input-email">
            </label>
            <x-customer-list :customers="$customers" />
        </x-modal>
    </section>
@endsection


@push('scripts')
    @vite('resources/js/invoice/invoice-create.js')
    @vite('resources/js/invoice/invoice-edit.js')
@endpush
