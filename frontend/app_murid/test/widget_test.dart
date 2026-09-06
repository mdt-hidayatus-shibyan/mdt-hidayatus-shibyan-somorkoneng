import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:app_murid/core/storage/storage_service.dart';
import 'package:app_murid/main.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  setUp(() async {
    SharedPreferences.setMockInitialValues({});
    await StorageService.init();
  });

  testWidgets('WaliApp smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(const WaliApp());
    expect(find.byType(WaliApp), findsOneWidget);
    await tester.pumpAndSettle(const Duration(seconds: 2));
  });
}
