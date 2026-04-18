<?php

namespace App\Controllers;

use App\Models\Announcement;
use App\Models\Absence;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected const LATE_GRACE_MINUTES = 5;
    protected const LATE_ALERT_MINUTES = 15;

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

    protected function createAnnouncement(
        string $title,
        string $content,
        int $createdBy,
        ?int $recipientUserId = null,
        ?string $recipientRole = null,
        string $category = 'general',
        ?int $relatedShiftId = null
    ): void {
        $announcementModel = new Announcement();
        $announcementModel->insert([
            'title' => $title,
            'content' => $content,
            'created_by' => $createdBy,
            'recipient_user_id' => $recipientUserId,
            'recipient_role' => $recipientRole,
            'category' => $category,
            'related_shift_id' => $relatedShiftId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function userHasApprovedAbsenceInRange(int $userId, string $rangeStart, string $rangeEnd): bool
    {
        $absenceModel = new Absence();

        return $absenceModel->where('user_id', $userId)
            ->where('status', 'Aprobado')
            ->where('start_date <=', date('Y-m-d', strtotime($rangeEnd)))
            ->where('end_date >=', date('Y-m-d', strtotime($rangeStart)))
            ->countAllResults() > 0;
    }
}
