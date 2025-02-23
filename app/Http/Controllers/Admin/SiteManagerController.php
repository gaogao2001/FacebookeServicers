<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\SiteManager\SiteManagerRepositoryInterface;
use Illuminate\Http\Request;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class SiteManagerController extends Controller
{
    protected $accountService;
    protected $siteManagerRepository;

    public function __construct(AccountService $accountService, SiteManagerRepositoryInterface $siteManagerRepository)
    {
        $this->accountService = $accountService;
        $this->siteManagerRepository = $siteManagerRepository;

        // Tạo dữ liệu mặc định nếu database trống
        $siteManager = $this->siteManagerRepository->findFirst();
        if (!$siteManager) {
            $siteManager = $this->siteManagerRepository->create([
                'name' => null,
                'meta_title' => null,
                'meta_description' => null,
                'meta_keywords' => null,
                'og_site_name' => null,
                'og_type' => null,
                'og_locale' => null,
                'og_locale_alternate' => null,
                'robots' => null,
                'favicon' => null,
                'logo' => null,
            ]);
        }

        View::share('siteManager', $siteManager);
    }

    public function index()
    {
        $siteManager = $this->siteManagerRepository->findFirst();
        return redirect()->route('site-manager.show', ['id' => $siteManager->_id]);
    }

    public function siteManager()
    {
        $user = $this->accountService->findOne(Auth::id());
        $siteManager = $this->siteManagerRepository->findAll();

        return view('admin.pages.site_manager', compact('user', 'siteManager'));
    }

    public function createSiteManager(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'og_site_name' => 'nullable|string|max:255',
            'og_type' => 'nullable|string|max:255',
            'og_locale' => 'nullable|string|max:255',
            'og_locale_alternate' => 'nullable|string|max:255',
            'robots' => 'nullable|string|max:255',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('favicon')) {
            try {
                $favicon = $request->file('favicon');
                $faviconPath = $favicon->store('public/favicons');
                $data['favicon'] = Storage::url($faviconPath);
            } catch (\Exception $e) {
                Log::error('Error uploading favicon: ' . $e->getMessage());
            }
        }

        if ($request->hasFile('logo')) {
            try {
                $logo = $request->file('logo');
                $logoPath = $logo->store('public/logos');
                $data['logo'] = Storage::url($logoPath);
            } catch (\Exception $e) {
                Log::error('Error uploading logo: ' . $e->getMessage());
            }
        }

        $siteManager = $this->siteManagerRepository->create($data);

        return response()->json($siteManager);
    }

    public function show($id)
    {
        $siteManager = $this->siteManagerRepository->findById($id);

        if (!$siteManager) {
            return redirect()->route('site-manager.index')->withErrors(['error' => 'Site Manager not found']);
        }

        $user = $this->accountService->findOne(Auth::id());

        return view('admin.pages.site_manager', compact('user', 'siteManager'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'og_site_name' => 'nullable|string|max:255',
            'og_type' => 'nullable|string|max:255',
            'og_locale' => 'nullable|string|max:255',
            'og_locale_alternate' => 'nullable|string|max:255',
            'robots' => 'nullable|string|max:255',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('favicon')) {
            try {
                $favicon = $request->file('favicon');
                $faviconPath = $favicon->store('favicons', 'public');
                $data['favicon'] = Storage::url($faviconPath);
            } catch (\Exception $e) {
                Log::error('Error uploading favicon: ' . $e->getMessage());
            }
        }

        if ($request->hasFile('logo')) {
            try {
                $logo = $request->file('logo');
                $logoPath = $logo->store('logos', 'public');
                $data['logo'] = Storage::url($logoPath);
            } catch (\Exception $e) {
                Log::error('Error uploading logo: ' . $e->getMessage());
            }
        }

        $siteManager = $this->siteManagerRepository->update($id, $data);

        return redirect()->route('site-manager.show', ['id' => $id])->with('success', 'Cập nhật thành công');
    }

    public function delete($id)
    {
        $siteManager = $this->siteManagerRepository->delete($id);

        if (!$siteManager) {
            return response()->json(['error' => 'Site Manager not found'], 404);
        }

        return response()->json($siteManager);
    }
}
