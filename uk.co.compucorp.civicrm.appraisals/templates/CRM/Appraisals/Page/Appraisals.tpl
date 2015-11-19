{assign var="module" value="appraisals" }
{assign var="prefix" value="appr-" }

<div id="bootstrap-theme">
    <div id="{$module}">
        <nav class="navbar navbar-default">
            <div class="row">
                <div class="col-md-6">
                    <ol class="breadcrumb">
                        <li>
                            <a href="#" class="navbar-link">CiviHR</a>
                        </li>
                        <li>
                            <a href="#" class="navbar-link">Appraisals</a>
                        </li>
                        <li class="active">
                            Dashboard
                        </li>
                    </ol>
                </div>
                <div class="col-md-offset-4 col-md-2">
                    <span class="navbar-text">
                        <a class="navbar-link">
                            <span class="fa fa-plus-circle"></span>
                            Add Assignment
                        </a>
                    </span>
                </div>
            </div>
        </nav>
        <section>
            <div class="row">
                <div class="col-md-2">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="active">
                            <a href="#">Dashboard</a>
                        </li>
                        <li>
                            <a href="#">Profile</a>
                        </li>
                        <li>
                            <a href="#">Messages</a>
                        </li>
                    </ul>
                </div>
                <main class="col-md-10">
                    <footer class="text-center">
                        <strong>CiviHR</strong>
                        <p>CiviHR is openly available under the GNU AGPL License
                    </footer>
                </main>
            </div>
        </section>
    </div>
</div>
{literal}
<script type="text/javascript">
    (function(){
        function apprInit(){
            document.dispatchEvent(typeof window.CustomEvent == "function" ? new CustomEvent('apprInit') : (function(){
                var e = document.createEvent('Event');
                e.initEvent('apprInit', true, true);
                return e;
            })());
        };
        apprInit();

        document.addEventListener('apprReady', function(){
            apprInit();
        });
    })();
</script>
{/literal}
