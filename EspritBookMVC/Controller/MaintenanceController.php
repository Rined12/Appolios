<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Service/AccountMaintenanceService.php';

/**
 * CLI maintenance tasks (user accounts). Invoke via Controller/cli.php only.
 */
class MaintenanceController extends BaseController
{
    private function requireCli(): void
    {
        if (PHP_SAPI !== 'cli') {
            http_response_code(403);
            echo "Forbidden: maintenance tasks are CLI-only.\n";
            exit(1);
        }
    }

    public function fixPasswords(): void
    {
        $this->requireCli();
        try {
            $m = new AccountMaintenanceService();
            echo $m->applyDefaultPasswords123();
        } catch (Throwable $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function setupTeachers(): void
    {
        $this->requireCli();
        try {
            $m = new AccountMaintenanceService();
            echo $m->ensureTeacherRoleAndSample();
        } catch (Throwable $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function fixAccounts(): void
    {
        $this->requireCli();
        try {
            $m = new AccountMaintenanceService();
            echo $m->recreateAllAccountsPassword('password');
        } catch (Throwable $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function resetAccounts(): void
    {
        $this->requireCli();
        try {
            $m = new AccountMaintenanceService();
            echo $m->resetThreeDefaultAccounts('password');
        } catch (Throwable $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function debugLogin(): void
    {
        $this->requireCli();
        try {
            $m = new AccountMaintenanceService();
            echo $m->debugLoginDumpAndReinit();
        } catch (Throwable $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function testAuth(): void
    {
        $this->requireCli();
        try {
            $m = new AccountMaintenanceService();
            echo $m->testAuthRepairDemo();
        } catch (Throwable $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function help(): void
    {
        $this->requireCli();
        echo "APPOLIOS maintenance (CLI)\n";
        echo "Usage: php EspritBookMVC/Controller/cli.php <command>\n\n";
        echo "Commands:\n";
        echo "  fix-passwords   Set admin123 / student123 / teacher123 on default accounts\n";
        echo "  setup-teachers  ALTER role ENUM + sample teacher\n";
        echo "  fix-accounts    DELETE all users, recreate 3 accounts with password \"password\"\n";
        echo "  reset           Delete + recreate 3 default accounts with password \"password\"\n";
        echo "  debug-login     Dump users, test hashes, reinit all to password \"password\"\n";
        echo "  test-auth       Verify/repair admin, teacher, student with password \"password\"\n";
    }
}
