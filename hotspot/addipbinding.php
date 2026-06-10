<?php
/**
 * Add IP Binding UI
 * Displays form to add new IP Binding
 */
if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
    exit;
} else {
    // Process form submission
    if (isset($_POST['save_binding'])) {
        $mac    = $_POST['mac'];
        $ip     = $_POST['ip'];
        $toip   = $_POST['toip'];
        $server = $_POST['server'];
        $type   = $_POST['type'];
        $comm   = $_POST['comment'];

        $data = [
            "mac-address" => $mac,
            "type"        => $type,
        ];
        
        if (!empty($ip)) {
            $data["address"] = $ip;
        }
        if (!empty($toip)) {
            $data["to-address"] = $toip;
        }
        if (!empty($server) && $server != "all") {
            $data["server"] = $server;
        }
        if (!empty($comm)) {
            $data["comment"] = $comm;
        }

        $result = $API->comm("/ip/hotspot/ip-binding/add", $data);
        $cek = json_encode($result);

        if (strpos(strtolower($cek), '!trap') !== false) {
            $error = str_replace(":", "\n", explode('"', $cek)[5]);
            echo "<script>window.location='./?hotspot=add-ipbinding&session=" . $session . "&info=Gagal|Gagal menambahkan binding|" . addslashes($error) . "'</script>";
            exit;
        } else {
            echo "<script>window.location='./?hotspot=ipbinding&session=" . $session . "&info=Berhasil|IP Binding untuk MAC " . $mac . " berhasil ditambahkan'</script>";
            exit;
        }
    }
}
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.ipb-container {
    font-family: 'Inter', sans-serif;
    width: 100%;
    margin: 0 auto;
}

.ipb-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248,250,252,0.9));
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.6);
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    overflow: hidden;
    animation: slideUp 0.4s ease;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

.ipb-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    color: white;
}

.ipb-header i {
    font-size: 20px;
    background: rgba(255,255,255,0.2);
    padding: 10px;
    border-radius: 12px;
}

.ipb-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
}

.ipb-header p {
    margin: 4px 0 0;
    opacity: 0.9;
    font-size: 14px;
}

.ipb-body {
    padding: 30px;
}

.modern-input-group {
    position: relative;
    margin-bottom: 24px;
}

.modern-input {
    width: 100%;
    padding: 16px 16px 16px 48px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-family: inherit;
    font-size: 15px;
    background: #fff;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.modern-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102,126,234,0.1);
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 18px;
    transition: color 0.3s;
}

.modern-input:focus + .input-icon {
    color: #667eea;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #475569;
    margin-bottom: 8px;
    font-size: 14px;
}

.row-2 {
    display: flex;
    gap: 20px;
}

.row-2 > div {
    flex: 1;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(102,126,234,0.3);
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
    border: none;
    padding: 14px 28px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
}

.btn-secondary:hover {
    background: #e2e8f0;
    color: #0f172a;
}

select.modern-input {
    appearance: none;
    padding-left: 16px;
}
</style>

<div class="ipb-container">
    <div class="ipb-card">
        <div class="ipb-header">
            <i class="fa fa-address-book"></i>
            <div>
                <h3>Add IP Binding</h3>
                <p>Bypass, Block, atau Regular Host</p>
            </div>
        </div>
        
        <div class="ipb-body">
            <form method="post" action="">
                
                <div class="form-label">MAC Address <span style="color:red">*</span></div>
                <div class="modern-input-group">
                    <input type="text" class="modern-input" name="mac" required placeholder="00:11:22:33:44:55" pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$" title="Format MAC Address: XX:XX:XX:XX:XX:XX">
                    <i class="fa fa-barcode input-icon"></i>
                </div>

                <div class="row-2">
                    <div>
                        <div class="form-label">IP Address (Optional)</div>
                        <div class="modern-input-group">
                            <input type="text" class="modern-input" name="ip" placeholder="192.168.1.10">
                            <i class="fa fa-sitemap input-icon"></i>
                        </div>
                    </div>
                    <div>
                        <div class="form-label">To Address (Optional)</div>
                        <div class="modern-input-group">
                            <input type="text" class="modern-input" name="toip" placeholder="192.168.1.10">
                            <i class="fa fa-arrow-right input-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="row-2">
                    <div>
                        <div class="form-label">Type</div>
                        <div class="modern-input-group">
                            <select class="modern-input" name="type" style="padding-left:16px;">
                                <option value="regular">Regular</option>
                                <option value="bypassed">Bypassed</option>
                                <option value="blocked">Blocked</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="form-label">Server</div>
                        <div class="modern-input-group">
                            <select class="modern-input" name="server" style="padding-left:16px;">
                                <option value="all">All</option>
                                <?php
                                $getserver = $API->comm("/ip/hotspot/profile/print");
                                foreach ($getserver as $s) {
                                    echo "<option value='".$s['name']."'>".$s['name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-label">Comment / Nama (Optional)</div>
                <div class="modern-input-group">
                    <input type="text" class="modern-input" name="comment" placeholder="Nama Perangkat / Pemilik">
                    <i class="fa fa-comment input-icon"></i>
                </div>

                <div style="margin-top: 30px; display: flex; gap: 12px;">
                    <button type="submit" name="save_binding" class="btn-primary">
                        <i class="fa fa-save"></i> Simpan Binding
                    </button>
                    <a href="./?hotspot=ipbinding&session=<?= $session ?>" class="btn-secondary">
                        <i class="fa fa-arrow-left"></i> Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
