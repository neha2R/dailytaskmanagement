@if ($data->lastPage() > 1)
<div class="dataTables_paginate paging_simple_numbers mt-4 d-flex justify-content-center align-items-center"
    id="cstm_tbl_paginate">
    <p class="me-2 fw-bold">Pages</p>
    <ul class="pagination custom-pagination justify-content-end mb-0">

        {{-- Previous Page Link --}}
        <li class="paginate_button page-item {{ $data->onFirstPage() ? 'disabled' : '' }}"
            id="cstm_tbl_previous">
            <a href="{{ $data->previousPageUrl() }}" aria-controls="cstm_tbl"
                data-dt-idx="previous" tabindex="0" class="page-link">Previous</a>
        </li>

        {{-- Show first page after "Previous" button --}}
        @if ($data->currentPage() == $data->lastPage())
            <li class="paginate_button page-item">
                <a href="{{ $data->url(1) }}" aria-controls="cstm_tbl" data-dt-idx="1"
                    tabindex="0" class="page-link">1</a>
            </li>
        @endif

        {{-- Pagination Links --}}
        @php
            $start = max(1, $data->currentPage() - 2);
            $end = min($data->lastPage(), $data->currentPage() + 2);
        @endphp

        {{-- Show page links --}}
        @for ($i = $start; $i <= $end; $i++)
            <li
                class="paginate_button page-item {{ $i == $data->currentPage() ? 'active' : '' }}">
                <a href="{{ $data->url($i) }}" aria-controls="cstm_tbl"
                    data-dt-idx="{{ $i }}" tabindex="0"
                    class="page-link">{{ $i }}</a>
            </li>
        @endfor

        {{-- Show "..." after the last page --}}
        @if ($data->currentPage() + 2 < $data->lastPage())
            <li class="paginate_button page-item disabled" id="cstm_tbl_ellipsis">
                <a href="#" aria-controls="cstm_tbl" data-dt-idx="ellipsis" tabindex="0"
                    class="page-link">…</a>
            </li>

            {{-- Show last page --}}
            <li class="paginate_button page-item">
                <a href="{{ $data->url($data->lastPage()) }}"
                    aria-controls="cstm_tbl" data-dt-idx="{{ $data->lastPage() }}"
                    tabindex="0" class="page-link">{{ $data->lastPage() }}</a>
            </li>
        @endif

        {{-- Next Page Link --}}
        <li class="paginate_button page-item {{ !$data->hasMorePages() ? 'disabled' : '' }}"
            id="cstm_tbl_next">
            <a href="{{ $data->nextPageUrl() }}" aria-controls="cstm_tbl"
                data-dt-idx="next" tabindex="0" class="page-link">Next</a>
        </li>
    </ul>
</div>
@endif