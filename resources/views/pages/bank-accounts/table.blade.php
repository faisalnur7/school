<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
        <h3 class="card-title mb-0 text-white text-lg">{{ __('Bank Accounts') }}</h3>
        <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
            {{ __('+ Add Bank Account') }}
        </a>
    </div>

    <div class="card-body px-0 pb-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Bank Name') }}</th>
                        <th>{{ __('Account Name') }}</th>
                        <th>{{ __('Account Number') }}</th>
                        <th>{{ __('Branch') }}</th>
                        <th>{{ __('Opening Balance') }}</th>
                        <th>{{ __('Opening Date') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th width="120">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bankAccounts as $account)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $account->bank_name }}</td>
                            <td>{{ $account->account_name }}</td>
                            <td><code style="font-size:12px;background:#f1f5f9;padding:2px 7px;border-radius:5px">{{ $account->account_number }}</code></td>
                            <td class="text-muted">{{ $account->branch_name ?? '—' }}</td>
                            <td class="mono fw-bold" style="color:#4338ca">{{ number_format($account->opening_balance, 2) }}</td>
                            <td class="mono" style="font-size:13px">{{ $account->opening_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $account->is_active ? 'success' : 'secondary' }}">
                                    {{ $account->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            <td style="display:flex;gap:5px; justify-content: center; align-items: center;;align-items:center">
                                <a href="{{ route('bank-accounts.edit', $account->id) }}" class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('bank-accounts.destroy', $account->id) }}" method="POST"
                                      class="btn btn-sm btn-danger d-inline m-0"
                                      onsubmit="return confirm('Delete this bank account?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;padding:0;color:inherit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($bankAccounts->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">{{ __('No bank accounts found') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-3 pt-3">{{ $bankAccounts->links() }}</div>
    </div>
</div>
